
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('daisyui'),
  ],
  daisyui: {
    themes: [
      {
        deepdive: {
          "primary": "#10B981",         // Emerald 500 (Vibrant Green for buttons)
          "secondary": "#374151",      // Gray 700
          "accent": "#34D399",         // Emerald 400 (for highlights)

          "neutral": "#1F2937",        // Gray 800
          "base-100": "#111827",        // Gray 900 (Main dark background)
          "base-content": "#F9FAFB",     // Gray 50 (Light text)

          "info": "#3ABFF8",
          "success": "#36D399",
          "warning": "#FBBD23",
          "error": "#F87272",

          "--rounded-box": "1rem",
          "--rounded-btn": "0.5rem",
        },
      },
      "saas",
      "farmhouse",
      "garden",
    ],
  },
}
