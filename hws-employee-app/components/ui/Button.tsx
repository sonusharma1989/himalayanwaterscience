import { ButtonHTMLAttributes, forwardRef } from "react";
import { cn } from "@/lib/utils";

type Variant = "primary" | "secondary" | "ghost" | "accent" | "danger";

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: Variant;
  size?: "default" | "sm";
  block?: boolean;
}

const variantClasses: Record<Variant, string> = {
  primary: "bg-aqua-600 text-white shadow-sm hover:bg-aqua-700",
  secondary: "border border-aqua-200 bg-white text-aqua-700 hover:bg-aqua-50",
  ghost: "bg-transparent text-slate-600 hover:bg-slate-100",
  accent: "bg-amber-500 text-white hover:bg-amber-600",
  danger: "bg-rose-600 text-white hover:bg-rose-700",
};

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(function Button(
  { variant = "primary", size = "default", block, className, children, ...props },
  ref
) {
  return (
    <button
      ref={ref}
      className={cn(
        "inline-flex select-none items-center justify-center gap-2 whitespace-nowrap font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-40",
        size === "sm" ? "rounded-lg px-3 py-1.5 text-xs" : "rounded-xl px-4 py-2.5 text-sm",
        block && "w-full",
        variantClasses[variant],
        className
      )}
      {...props}
    >
      {children}
    </button>
  );
});
