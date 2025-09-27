import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            // Added page-specific entry for artwork show page scripts
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/pages/art-show.js",
            ],
            refresh: true,
        }),
    ],
});
