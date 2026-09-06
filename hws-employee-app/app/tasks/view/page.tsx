"use client";

import { Suspense } from "react";
import { useSearchParams } from "next/navigation";
import { TaskDetailClient } from "../[id]/TaskDetailClient";

function TaskDetailWrapper() {
  const searchParams = useSearchParams();
  const id = searchParams.get("id") || "0";

  return <TaskDetailClient key={id} id={id} />;
}

export default function TaskViewPage() {
  return (
    <Suspense fallback={<div className="p-6 text-center text-sm text-slate-500">Loading task...</div>}>
      <TaskDetailWrapper />
    </Suspense>
  );
}
