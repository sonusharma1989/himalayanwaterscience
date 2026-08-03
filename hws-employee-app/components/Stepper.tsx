import { Check } from "lucide-react";
import { STEP_LABELS } from "@/lib/data";
import { cn } from "@/lib/utils";

export function Stepper({ step }: { step: number }) {
  return (
    <div className="mb-6 flex w-full items-start">
      {STEP_LABELS.map((label, i) => {
        const done = i < step;
        const active = i === step;
        return (
          <div key={label} className="contents">
            <div className="flex shrink-0 flex-col items-center gap-1" style={{ width: 44 }}>
              <div
                className={cn(
                  "flex h-7 w-7 items-center justify-center rounded-full text-[11px] font-bold",
                  done && "bg-aqua-600 text-white",
                  active && "bg-aqua-600 text-white ring-4 ring-aqua-100",
                  !done && !active && "bg-slate-100 text-slate-400"
                )}
              >
                {done ? <Check className="h-3.5 w-3.5" strokeWidth={2.5} /> : i + 1}
              </div>
              <span
                className={cn(
                  "text-center text-[9px] font-bold",
                  done || active ? "text-slate-600" : "text-slate-300"
                )}
              >
                {label}
              </span>
            </div>
            {i < STEP_LABELS.length - 1 && (
              <div
                className={cn(
                  "mx-0.5 mt-3.5 h-0.5 flex-1",
                  i < step ? "bg-aqua-600" : "bg-slate-200"
                )}
              />
            )}
          </div>
        );
      })}
    </div>
  );
}
