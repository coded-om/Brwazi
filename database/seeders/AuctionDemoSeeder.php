<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Artwork;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AuctionDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Admin reference (optional)
        $adminId = Admin::where('email', 'admin@brwaze.com')->value('id');

        // Create demo users
        $owner = User::firstOrCreate(
            ['email' => 'artist@example.com'],
            [
                'fname' => 'فنان',
                'lname' => 'تجريبي',
                'password' => Hash::make('password'),
                'status' => User::STATUS_VERIFIED,
            ]
        );

        $bidder1 = User::firstOrCreate(
            ['email' => 'bidder1@example.com'],
            [
                'fname' => 'مزايد',
                'lname' => 'أول',
                'password' => Hash::make('password'),
                'status' => User::STATUS_VERIFIED,
            ]
        );

        $bidder2 = User::firstOrCreate(
            ['email' => 'bidder2@example.com'],
            [
                'fname' => 'مزايد',
                'lname' => 'ثاني',
                'password' => Hash::make('password'),
                'status' => User::STATUS_VERIFIED,
            ]
        );

        // Create artworks
        $art1 = Artwork::firstOrCreate(
            ['user_id' => $owner->id, 'title' => 'سيدات القمر'],
            [
                'category' => 'traditional',
                'type' => 'painting',
                'description' => 'لوحة تجريبية للعرض المباشر',
                'status' => 'published',
                'auction_start_price' => 100,
                'year' => now()->year,
                'dimensions' => '50x70 cm',
            ]
        );

        $art2 = Artwork::firstOrCreate(
            ['user_id' => $owner->id, 'title' => 'ضوء الفجر'],
            [
                'category' => 'traditional',
                'type' => 'painting',
                'description' => 'لوحة مجدولة قريباً',
                'status' => 'published',
                'auction_start_price' => 200,
                'year' => now()->year,
                'dimensions' => '60x80 cm',
            ]
        );

        $art3 = Artwork::firstOrCreate(
            ['user_id' => $owner->id, 'title' => 'غروب هادئ'],
            [
                'category' => 'traditional',
                'type' => 'painting',
                'description' => 'لوحة انتهى مزادها',
                'status' => 'published',
                'auction_start_price' => 300,
                'year' => now()->year,
                'dimensions' => '40x60 cm',
            ]
        );

        // Live auction with bids
        $live = Auction::firstOrCreate(
            ['artwork_id' => $art1->id],
            [
                'status' => 'live',
                'starts_at' => now()->subHour(),
                'ends_at' => now()->addHours(2),
                'start_price' => 100,
                'bid_increment' => 10,
                'reserve_price' => null,
                'buy_now_price' => null,
                'highest_bid_amount' => 100,
                'bids_count' => 0,
                'approved_by_admin_id' => $adminId,
            ]
        );

        if (Bid::where('auction_id', $live->id)->count() === 0) {
            $b1 = Bid::create(['auction_id' => $live->id, 'user_id' => $bidder1->id, 'amount' => 120]);
            $b2 = Bid::create(['auction_id' => $live->id, 'user_id' => $bidder2->id, 'amount' => 140]);
            $live->update([
                'highest_bid_id' => $b2->id,
                'highest_bid_amount' => 140,
                'bids_count' => 2,
            ]);
        }

        // Scheduled auction
        Auction::firstOrCreate(
            ['artwork_id' => $art2->id],
            [
                'status' => 'scheduled',
                'starts_at' => now()->addHours(3),
                'ends_at' => now()->addHours(4),
                'start_price' => 200,
                'bid_increment' => 20,
                'reserve_price' => 350,
                'buy_now_price' => null,
                'highest_bid_amount' => 0,
                'bids_count' => 0,
                'approved_by_admin_id' => $adminId,
            ]
        );

        // Ended auction (sold)
        $ended = Auction::firstOrCreate(
            ['artwork_id' => $art3->id],
            [
                'status' => 'ended',
                'starts_at' => now()->subDays(2),
                'ends_at' => now()->subDay(),
                'start_price' => 300,
                'bid_increment' => 25,
                'reserve_price' => 320,
                'buy_now_price' => null,
                'highest_bid_amount' => 0,
                'bids_count' => 0,
                'approved_by_admin_id' => $adminId,
            ]
        );
        if (Bid::where('auction_id', $ended->id)->count() === 0) {
            $b1 = Bid::create(['auction_id' => $ended->id, 'user_id' => $bidder1->id, 'amount' => 310]);
            $b2 = Bid::create(['auction_id' => $ended->id, 'user_id' => $bidder2->id, 'amount' => 340]);
            $ended->update([
                'highest_bid_id' => $b2->id,
                'highest_bid_amount' => 340,
                'bids_count' => 2,
            ]);
        }
    }
}
