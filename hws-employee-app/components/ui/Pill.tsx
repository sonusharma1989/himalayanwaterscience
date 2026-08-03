import { ButtonHTMLAttributes } from "react";
import { cn } from "@/lib/utils";

interface PillProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  active?: boolean;
}

export function Pill({ active, className, children, ...props }: PillProps) {
  return (
    <button
      type="button"
      className={cn(
        "shrink-0 rounded-full border px-3 py-1.5 text-xs font-semibold transition-colors",
        active
          ? "border-aqua-600 bg-aqua-600 text-white"
          : "border-slate-200 bg-white text-slate-500 hover:border-aqua-300 hover:text-aqua-700",
        className
      )}
      {...props}
    >
      {children}
    </button>
  );
}
