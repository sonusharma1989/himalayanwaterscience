"use client";

import { Star } from "lucide-react";
import { cn } from "@/lib/utils";

export function StarRating({
  value,
  onChange,
}: {
  value: number;
  onChange: (n: number) => void;
}) {
  return (
    <div className="flex gap-1">
      {[1, 2, 3, 4, 5].map((n) => (
        <button key={n} type="button" onClick={() => onChange(n)} aria-label={`Rate ${n} stars`}>
          <Star
            className={cn("h-6 w-6", n <= value ? "text-amber-400" : "text-slate-300")}
            fill={n <= value ? "currentColor" : "none"}
            strokeWidth={1.75}
          />
        </button>
      ))}
    </div>
  );
}
