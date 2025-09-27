/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./public/**/*.html",
        "./app/**/*.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                arabic: ["DINNextLTArabic", "Arial", "sans-serif"],
            },
            colors: {
                brwazi: {
                    purple: "#6161ab",
                    dark: "#141640",
                    pink: "#9B4F9F",
                    blue: "#2563eb",
                    gold: "#f59e0b",
                    emerald: "#10b981",
                },
                // Custom gradient colors
                gradient: {
                    50: "#fdf2f8",
                    100: "#fce7f3",
                    200: "#fbcfe8",
                    300: "#f9a8d4",
                    400: "#f472b6",
                    500: "#ec4899",
                    600: "#db2777",
                    700: "#be185d",
                    800: "#9d174d",
                    900: "#831843",
                },
                // Auction status colors
                auction: {
                    live: "#ef4444",
                    soon: "#f59e0b",
                    ended: "#6b7280",
                },
                // Category specific colors
                category: {
                    art: "#8b5cf6",
                    literature: "#06b6d4",
                    gallery: "#10b981",
                    auction: "#f59e0b",
                },
                // Status colors
                status: {
                    success: "#10b981",
                    warning: "#f59e0b",
                    error: "#ef4444",
                    info: "#3b82f6",
                },
            },
        },
    },
    plugins: [],
};
