import type { Config } from "tailwindcss";

const config: Config = {
  content: ["./app/**/*.{ts,tsx}", "./components/**/*.{ts,tsx}", "./lib/**/*.{ts,tsx}"],
  theme: {
    extend: {
      colors: {
        // Brand palette lifted from the prototype's design system.
        aqua: {
          50: "#eefbfb",
          100: "#d3f3f4",
          200: "#a8e6e9",
          300: "#74d2d8",
          400: "#43b6bf",
          500: "#2a98a3",
          600: "#1d7c88",
          700: "#1b6470",
          800: "#1a505a",
          900: "#17434b",
        },
      },
      fontFamily: {
        display: ["var(--font-space-grotesk)", "ui-sans-serif", "system-ui", "sans-serif"],
        sans: [
          "-apple-system",
          "BlinkMacSystemFont",
          "Segoe UI",
          "Roboto",
          "Helvetica Neue",
          "Arial",
          "sans-serif",
        ],
      },
      boxShadow: {
        card: "0 1px 2px rgba(15,23,42,.04), 0 1px 3px rgba(15,23,42,.06)",
      },
      borderRadius: {
        "4xl": "2.25rem",
        "5xl": "3rem",
      },
    },
  },
  plugins: [],
};

export default config;
