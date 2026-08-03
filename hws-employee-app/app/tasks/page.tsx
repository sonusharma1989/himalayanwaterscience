"use client";

import { useMemo, useState } from "react";
import { Search } from "lucide-react";
import { useApp } from "@/lib/store";
import { statusCategory } from "@/lib/data";
import { TaskFilter } from "@/lib/types";
import { Card } from "@/components/ui/Card";
import { Pill } from "@/components/ui/Pill";
import { FieldInput } from "@/components/ui/Field";
import { TaskCard } from "@/components/TaskCard";

const FILTERS: { key: TaskFilter; label: string }[] = [
  { key: "all", label: "All" },
  { key: "pending", label: "Pending" },
  { key: "progress", label: "In progress" },
  { key: "done", label: "Completed" },
];

export default function TasksPage() {
  const { tasks } = useApp();
  const [filter, setFilter] = useState<TaskFilter>("all");
  const [query, setQuery] = useState("");

  const visibleTasks = useMemo(() => {
    const q = query.trim().toLowerCase();
    return tasks.filter((t) => {
      const matchesFilter = filter === "all" || statusCategory(t.step) === filter;
      const matchesQuery =
        q.length === 0 ||
        t.name.toLowerCase().includes(q) ||
        t.address.toLowerCase().includes(q);
      return matchesFilter && matchesQuery;
    });
  }, [tasks, filter, query]);

  return (
    <div className="flex-1 px-5 pb-6 pt-5">
      <p className="mb-4 font-display text-lg font-bold text-slate-800">My Tasks</p>

      <div className="relative mb-3">
        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" strokeWidth={1.75} />
        <FieldInput
          className="pl-9"
          placeholder="Search by customer, address..."
          value={query}
          onChange={(e) => setQuery(e.target.value)}
        />
      </div>

      <div className="mb-4 flex gap-2 overflow-x-auto pb-1">
        {FILTERS.map((f) => (
          <Pill key={f.key} active={filter === f.key} onClick={() => setFilter(f.key)}>
            {f.label}
          </Pill>
        ))}
      </div>

      {visibleTasks.length > 0 ? (
        <Card className="divide-y divide-slate-100 overflow-hidden">
          {visibleTasks.map((t) => (
            <TaskCard key={t.id} task={t} />
          ))}
        </Card>
      ) : (
        <Card className="p-6 text-center text-sm text-slate-400">
          No tasks match your search.
        </Card>
      )}
    </div>
  );
}
