import type { Metadata, Viewport } from "next";
import { Space_Grotesk } from "next/font/google";
import "./globals.css";
import { AppProvider } from "@/lib/store";
import { BottomNav } from "@/components/BottomNav";

const spaceGrotesk = Space_Grotesk({
  subsets: ["latin"],
  weight: ["500", "600", "700"],
  variable: "--font-space-grotesk",
  display: "swap",
});

export const metadata: Metadata = {
  title: "Himalayan Water Science — Employee App",
  description:
    "Field service app for Himalayan Water Science technicians: attendance, tasks, and site surveys.",
};

export const viewport: Viewport = {
  width: "device-width",
  initialScale: 1,
  themeColor: "#1d7c88",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={spaceGrotesk.variable}>
      <body className="font-sans antialiased">
        <AppProvider>
          <div className="mx-auto flex min-h-screen max-w-md flex-col bg-slate-50 sm:border-x sm:border-slate-200">
            {children}
            <BottomNav />
          </div>
        </AppProvider>
      </body>
    </html>
  );
}
