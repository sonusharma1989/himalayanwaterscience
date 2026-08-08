"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { ArrowLeft, Bell, Check, CheckCheck } from "lucide-react";
import { useApp } from "@/lib/store";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";

export default function NotificationsPage() {
  const router = useRouter();
  const { notifications, unreadCount, fetchNotifications, markNotificationsRead } = useApp();

  useEffect(() => {
    fetchNotifications();
  }, []);

  function formatTime(dateStr: string) {
    try {
      const date = new Date(dateStr);
      return date.toLocaleDateString("en-IN", {
        day: "numeric",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
      });
    } catch (e) {
      return "";
    }
  }

  return (
    <div className="flex flex-1 flex-col bg-slate-50 min-h-screen">
      {/* Header */}
      <div className="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white/95 px-4 py-3 backdrop-blur">
        <div className="flex items-center gap-3">
          <button
            onClick={() => router.push("/home")}
            className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full hover:bg-slate-100 transition-colors"
          >
            <ArrowLeft className="h-5 w-5 text-slate-600" strokeWidth={1.75} />
          </button>
          <div>
            <p className="text-sm font-bold text-slate-800">Notifications</p>
            {unreadCount > 0 && (
              <p className="text-xs font-medium text-slate-400">{unreadCount} unread</p>
            )}
          </div>
        </div>

        {unreadCount > 0 && (
          <button
            onClick={() => markNotificationsRead()}
            className="flex items-center gap-1 text-xs font-bold text-aqua-600 hover:text-aqua-700"
          >
            <CheckCheck className="h-4 w-4" />
            Mark all read
          </button>
        )}
      </div>

      {/* Notifications List */}
      <div className="flex-1 px-4 py-4 space-y-3">
        {notifications.length === 0 ? (
          <div className="flex flex-col items-center justify-center py-20 text-center">
            <div className="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-4">
              <Bell className="h-8 w-8" strokeWidth={1.5} />
            </div>
            <p className="text-sm font-bold text-slate-700">No notifications yet</p>
            <p className="text-xs text-slate-400 mt-1 max-w-[240px]">
              We will notify you when you have new tasks or updates.
            </p>
          </div>
        ) : (
          notifications.map((notif) => (
            <Card
              key={notif.id}
              onClick={() => {
                if (!notif.is_read) {
                  markNotificationsRead(notif.id);
                }
              }}
              className={`p-4 flex gap-3 transition-colors relative cursor-pointer hover:bg-slate-100/50 ${
                notif.is_read ? "bg-white" : "bg-white border-l-4 border-l-aqua-500"
              }`}
            >
              {/* Icon Container */}
              <div
                className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-full ${
                  notif.is_read ? "bg-slate-100 text-slate-500" : "bg-aqua-50 text-aqua-600"
                }`}
              >
                <Bell className="h-5 w-5" strokeWidth={1.75} />
              </div>

              {/* Content */}
              <div className="flex-1 min-w-0 pr-4">
                <p className={`text-sm font-semibold text-slate-800 ${!notif.is_read ? "font-bold" : ""}`}>
                  {notif.title}
                </p>
                <p className="text-xs text-slate-500 mt-0.5 leading-relaxed">{notif.message}</p>
                <p className="text-[10px] font-medium text-slate-400 mt-2">
                  {formatTime(notif.created_at)}
                </p>
              </div>

              {/* Unread indicator / action */}
              {!notif.is_read && (
                <div className="absolute right-4 top-4 flex items-center justify-center">
                  <span className="h-2 w-2 rounded-full bg-aqua-500" />
                </div>
              )}
            </Card>
          ))
        )}
      </div>
    </div>
  );
}
