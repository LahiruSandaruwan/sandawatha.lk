/** @type {import("tailwindcss").Config} */
module.exports = {
  content: [
    "./public/**/*.{php,html,js}",
    "./app/views/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: "#FF4B91",
        secondary: "#FF7676",
        accent: "#FFE5E5"
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "sans-serif"]
      }
    }
  },
  plugins: [
    require("@tailwindcss/forms")
  ]
}