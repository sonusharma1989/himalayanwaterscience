import { HTMLAttributes } from "react";
import { cn } from "@/lib/utils";

export type BadgeVariant =
  | "service"
  | "install"
  | "amc"
  | "complaint"
  | "sales"
  | "survey"
  | "neutral";

const variantClasses: Record<BadgeVariant, string> = {
  service: "bg-sky-100 text-sky-700",
  install: "bg-violet-100 text-violet-700",
  amc: "bg-emerald-100 text-emerald-700",
  complaint: "bg-rose-100 text-rose-700",
  sales: "bg-amber-100 text-amber-700",
  survey: "bg-fuchsia-100 text-fuchsia-700",
  neutral: "bg-slate-100 text-slate-600",
};

interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
  variant?: BadgeVariant;
}

export function Badge({ variant = "neutral", className, ...props }: BadgeProps) {
  return (
    <span
      className={cn(
        "inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10.5px] font-bold uppercase tracking-wide",
        variantClasses[variant],
        className
      )}
      {...props}
    />
  );
}
