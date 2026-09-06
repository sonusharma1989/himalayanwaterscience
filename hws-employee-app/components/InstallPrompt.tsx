"use client";

import { useEffect, useState } from "react";
import { Download, X, Share } from "lucide-react";
import { useApp } from "@/lib/store";

export function InstallPrompt() {
  const { user } = useApp();
  const [deferredPrompt, setDeferredPrompt] = useState<any>(null);
  const [showPrompt, setShowPrompt] = useState(false);
  const [isIOS, setIsIOS] = useState(false);
  const [isDismissed, setIsDismissed] = useState(false);

  useEffect(() => {
    // Check if already running in standalone PWA mode (already installed)
    const isStandalone =
      window.matchMedia("(display-mode: standalone)").matches ||
      (window.navigator as any).standalone === true;

    if (isStandalone) {
      return;
    }

    // Check if device is iOS (iPhone/iPad Safari)
    const userAgent = window.navigator.userAgent.toLowerCase();
    const isIosDevice = /iphone|ipad|ipod/.test(userAgent);
    setIsIOS(isIosDevice);

    const handler = (e: Event) => {
      e.preventDefault();
      setDeferredPrompt(e);
      setShowPrompt(true);
    };

    window.addEventListener("beforeinstallprompt", handler);

    // Fallback: If on mobile browser and not standalone, show prompt after 1.5 seconds
    const timer = setTimeout(() => {
      const isMobile = /android|iphone|ipad|ipod/.test(userAgent);
      if (isMobile && !isStandalone) {
        setShowPrompt(true);
      }
    }, 1500);

    return () => {
      window.removeEventListener("beforeinstallprompt", handler);
      clearTimeout(timer);
    };
  }, []);

  // Show only when user is logged in, prompt is active, and not dismissed
  if (!user || !showPrompt || isDismissed) {
    return null;
  }

  const handleInstallClick = async () => {
    if (deferredPrompt) {
      deferredPrompt.prompt();
      const { outcome } = await deferredPrompt.userChoice;
      if (outcome === "accepted") {
        setShowPrompt(false);
      }
      setDeferredPrompt(null);
    } else {
      // If browser doesn't trigger prompt event automatically (e.g. Chrome on some devices or already engaged)
      alert(
        isIOS
          ? "Tap the Share button (square with arrow) below, then select 'Add to Home Screen'."
          : "Tap browser menu (3 dots at top right) and select 'Install app' or 'Add to Home screen'."
      );
    }
  };

  return (
    <div className="fixed bottom-16 left-0 right-0 z-50 px-4 pb-2">
      <div className="mx-auto flex max-w-md items-center justify-between gap-3 rounded-2xl border border-aqua-500/30 bg-slate-900/95 p-3.5 text-white shadow-2xl backdrop-blur-md">
        <div className="flex items-center gap-3">
          <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-aqua-600 text-white shadow-md">
            <Download className="h-5 w-5" strokeWidth={2.2} />
          </div>
          <div>
            <p className="text-xs font-bold leading-tight text-white">Install HWS App</p>
            <p className="text-[11px] text-slate-300">
              Add to Home Screen for fast access
            </p>
          </div>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={handleInstallClick}
            className="rounded-xl bg-aqua-500 px-3.5 py-1.5 text-xs font-bold text-white shadow hover:bg-aqua-400 active:scale-95 transition-all"
          >
            Install
          </button>
          <button
            onClick={() => setIsDismissed(true)}
            className="p-1 text-slate-400 hover:text-white transition-colors"
            title="Dismiss"
          >
            <X className="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  );
}
