import { cn } from "@/lib/utils";
import { TaskStatus } from "@/lib/types";

const variantClasses: Record<TaskStatus, string> = {
  pending: "bg-slate-100 text-slate-500",
  progress: "bg-amber-100 text-amber-700",
  done: "bg-emerald-100 text-emerald-700",
};

const labels: Record<TaskStatus, string> = {
  pending: "Pending",
  progress: "In progress",
  done: "Completed",
};

export function StatusPill({ status, className }: { status: TaskStatus; className?: string }) {
  return (
    <span
      className={cn(
        "inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10.5px] font-bold",
        variantClasses[status],
        className
      )}
    >
      {labels[status]}
    </span>
  );
}
