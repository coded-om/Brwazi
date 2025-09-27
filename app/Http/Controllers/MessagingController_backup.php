<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ArtworkRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MessagingController extends Controller
{
    /**
     * عرض صفحة المراسلات الرئيسية
     */
    public function index()
    {
        $user = Auth::user();

        // الحصول على المحادثات مع آخر رسالة
        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with(['user1', 'user2', 'lastMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        // تحديد المستخدم الآخر في كل محادثة
        $conversations->map(function ($conversation) use ($user) {
            $conversation->otherUser = $conversation->getOtherUser($user->id);
            $conversation->unreadCount = $conversation->unreadMessagesCount($user->id);
            return $conversation;
        });

        return view('userViwes.messages', compact('conversations'));
    }

    /**
     * عرض محادثة محددة
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        // التحقق من أن المستخدم جزء من المحادثة
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            abort(403, 'غير مسموح لك بالوصول لهذه المحادثة');
        }

        // تحديد المستخدم الآخر
        $otherUser = $conversation->getOtherUser($user->id);

        // الحصول على الرسائل مع العلاقات
        $messages = $conversation->messages()
            ->with(['sender', 'artworkRequest'])
            ->get();

        // تعليم الرسائل كمقروءة
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('userViwes.conversation', compact('conversation', 'messages', 'otherUser'));
    }

    /**
     * بدء محادثة جديدة مع مستخدم
     */
    public function startConversation(Request $request)
    {
        $user = Auth::user();
        $otherUserId = $request->user_id;

        // التحقق من وجود المستخدم
        $otherUser = User::find($otherUserId);
        if (!$otherUser) {
            return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
        }

        // منع المستخدم من بدء محادثة مع نفسه
        if ($otherUserId == $user->id) {
            return response()->json(['success' => false, 'message' => 'لا يمكنك بدء محادثة مع نفسك'], 400);
        }

        // إنشاء أو الحصول على المحادثة
        $conversation = Conversation::createOrGet($user->id, $otherUserId);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id
        ]);
    }

    /**
     * البحث عن المستخدمين
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('query');
        $user = Auth::user();

        if (!$query || strlen($query) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::where('id', '!=', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('full_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'full_name', 'email', 'ProfileImage')
            ->limit(10)
            ->get();

        return response()->json(['users' => $users]);
    }

    /**
     * إرسال رسالة نصية
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // التحقق من صحة الطلب
        $request->validate([
            'content' => 'required_without:image|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        // التحقق من أن المستخدم جزء من المحادثة
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            abort(403, 'غير مسموح لك بإرسال رسائل في هذه المحادثة');
        }

        $imagePath = null;

        // رفع الصورة إن وجدت
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('messages', 'public');
        }

        // تحديد نوع الرسالة
        $messageType = Message::determineType($request->content, $imagePath);

        // إنشاء الرسالة
        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'content' => $request->content,
            'image_path' => $imagePath,
            'type' => $messageType,
        ]);

        // تحديث وقت آخر رسالة في المحادثة
        $conversation->updateLastMessageTime();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'image_path' => $message->image_path,
                    'created_at' => $message->created_at->toISOString(),
                    'sender' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'profile_picture' => $user->ProfileImage,
                    ]
                ]
            ]);
        }

        return redirect()->back()->with('success', 'تم إرسال الرسالة بنجاح');
    }

    /**
     * إرسال طلب لوحة فنية
     */
    public function sendArtworkRequest(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // التحقق من صحة الطلب
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'budget' => 'required|numeric|min:1',
            'deadline' => 'required|date|after:today',
            'reference_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // التحقق من أن المستخدم جزء من المحادثة
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // إنشاء رسالة خاصة بطلب اللوحة
            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'content' => 'طلب لوحة فنية: ' . $request->title,
                'type' => 'artwork_request',
            ]);

            // رفع الصور المرجعية
            $referenceImages = [];
            if ($request->hasFile('reference_images')) {
                foreach ($request->file('reference_images') as $image) {
                    $path = $image->store('artwork-requests', 'public');
                    $referenceImages[] = $path;
                }
            }

            // إنشاء طلب اللوحة
            $artworkRequest = ArtworkRequest::create([
                'message_id' => $message->id,
                'requester_id' => $user->id,
                'artist_id' => $conversation->getOtherUser($user->id)->id,
                'title' => $request->title,
                'description' => $request->description,
                'budget' => $request->budget,
                'deadline' => $request->deadline,
                'reference_images' => $referenceImages ? json_encode($referenceImages) : null,
                'status' => 'pending',
            ]);

            // تحديث وقت آخر رسالة
            $conversation->updateLastMessageTime();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال طلب اللوحة بنجاح',
                    'artwork_request' => $artworkRequest->load('message')
                ]);
            }

            return redirect()->back()->with('success', 'تم إرسال طلب اللوحة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إرسال الطلب'], 500);
            }

            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء إرسال الطلب']);
        }
    }

    /**
     * الرد على طلب لوحة فنية (قبول أو رفض)
     */
    public function respondToArtworkRequest(Request $request, ArtworkRequest $artworkRequest)
    {
        $user = Auth::user();

        // التحقق من أن المستخدم هو الفنان المطلوب
        if ($artworkRequest->artist_id !== $user->id) {
            abort(403, 'غير مسموح لك بالرد على هذا الطلب');
        }

        // التحقق من أن الطلب ما زال قيد الانتظار
        if ($artworkRequest->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'هذا الطلب تم الرد عليه من قبل']);
        }

        $request->validate([
            'response' => 'required|in:accepted,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        // تحديث حالة الطلب
        $artworkRequest->update([
            'status' => $request->response,
            'response_notes' => $request->notes,
            'responded_at' => now(),
        ]);

        // إنشاء رسالة إعلامية في المحادثة
        $responseMessage = $request->response === 'accepted' ? 'تم قبول طلب اللوحة' : 'تم رفض طلب اللوحة';
        if ($request->notes) {
            $responseMessage .= ': ' . $request->notes;
        }

        $artworkRequest->message->conversation->messages()->create([
            'sender_id' => $user->id,
            'content' => $responseMessage,
            'type' => 'text',
        ]);

        // تحديث وقت آخر رسالة
        $artworkRequest->message->conversation->updateLastMessageTime();

        return redirect()->back()->with('success', 'تم تسجيل ردك على طلب اللوحة');
    }

    /**
     * تعليم جميع الرسائل في المحادثة كمقروءة
     */
    public function markAsRead(Conversation $conversation)
    {
        $user = Auth::user();

        // التحقق من أن المستخدم جزء من المحادثة
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            abort(403);
        }

        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * الحصول على الرسائل الجديدة في المحادثة (للـ live messaging)
     */
    public function getNewMessages(Conversation $conversation)
    {
        $user = Auth::user();

        // التحقق من أن المستخدم جزء من المحادثة
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            abort(403);
        }

        $afterId = request('after_id', 0);

        $messages = $conversation->messages()
            ->with(['sender', 'artworkRequest'])
            ->where('id', '>', $afterId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'image_path' => $message->image_path,
                    'created_at' => $message->created_at->toISOString(),
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'profile_picture' => $message->sender->profile_picture,
                    ]
                ];
            })
        ]);
    }
}
