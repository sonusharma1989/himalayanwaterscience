import { TaskDetailClient } from "./TaskDetailClient";

export default async function TaskDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  // Keying by id forces a clean remount (and fresh local state — photos,
  // signature, rating) whenever the technician opens a different task.
  return <TaskDetailClient key={id} id={id} />;
}
