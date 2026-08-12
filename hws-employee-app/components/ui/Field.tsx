import {
  InputHTMLAttributes,
  LabelHTMLAttributes,
  TextareaHTMLAttributes,
  forwardRef,
} from "react";
import { cn } from "@/lib/utils";

export function FieldLabel({ className, ...props }: LabelHTMLAttributes<HTMLLabelElement>) {
  return (
    <label
      className={cn(
        "mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500",
        className
      )}
      {...props}
    />
  );
}

const fieldStyles =
  "w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition-colors focus:border-aqua-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-aqua-500/30 disabled:opacity-60 disabled:cursor-not-allowed read-only:bg-slate-100/80 read-only:text-slate-500 read-only:cursor-not-allowed";

export const FieldInput = forwardRef<HTMLInputElement, InputHTMLAttributes<HTMLInputElement>>(
  function FieldInput({ className, ...props }, ref) {
    return <input ref={ref} className={cn(fieldStyles, className)} {...props} />;
  }
);

export const FieldTextarea = forwardRef<
  HTMLTextAreaElement,
  TextareaHTMLAttributes<HTMLTextAreaElement>
>(function FieldTextarea({ className, ...props }, ref) {
  return <textarea ref={ref} className={cn(fieldStyles, "resize-none", className)} {...props} />;
});
