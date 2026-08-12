import Link from "next/link";
import { ChevronRight } from "lucide-react";
import { Task } from "@/lib/types";
import { statusCategory, priorityBarClass, badgeVariantForType } from "@/lib/data";
import { Badge } from "@/components/ui/Badge";
import { StatusPill } from "@/components/ui/StatusPill";

export function TaskCard({ task }: { task: Task }) {
  const href = task.isSurvey ? `/survey?taskId=${task.id}` : `/tasks/${task.id}`;

  return (
    <Link
      href={href}
      data-status={statusCategory(task.step)}
      className="flex items-stretch gap-3 p-3.5 text-left transition-colors hover:bg-slate-50"
    >
      <div className={`w-1 rounded-full ${priorityBarClass(task.priority)}`} />
      <div className="min-w-0 flex-1">
        <div className="flex items-center justify-between gap-2">
          <p className="truncate text-sm font-bold text-slate-800">{task.name}</p>
          <span className="shrink-0 text-[11px] font-medium text-slate-400">{task.time}</span>
        </div>
        <p className="mt-0.5 truncate text-xs text-slate-400">{task.address}</p>
        <div className="mt-2 flex flex-wrap items-center gap-1.5">
          <Badge variant={badgeVariantForType(task.type)}>{task.type}</Badge>
          <StatusPill status={statusCategory(task.step)} />
        </div>
      </div>
      <ChevronRight className="h-5 w-5 shrink-0 self-center text-slate-300" strokeWidth={1.75} />
    </Link>
  );
}
