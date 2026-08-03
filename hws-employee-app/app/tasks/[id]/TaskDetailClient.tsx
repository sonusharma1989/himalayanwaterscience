"use client";

import { useRef, useState } from "react";
import { useRouter } from "next/navigation";
import { ArrowLeft, MapPin, Phone, Pencil } from "lucide-react";
import { useApp } from "@/lib/store";
import { STEP_ACTIONS, badgeVariantForType } from "@/lib/data";
import { Card } from "@/components/ui/Card";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import { FieldLabel, FieldTextarea } from "@/components/ui/Field";
import { Stepper } from "@/components/Stepper";
import { PhotoUploadGrid } from "@/components/PhotoUploadGrid";
import { StarRating } from "@/components/StarRating";
import { SignaturePad, SignaturePadHandle } from "@/components/SignaturePad";

export function TaskDetailClient({ id }: { id: string }) {
  const router = useRouter();
  const { tasks, advanceTaskStep } = useApp();
  const task = tasks.find((t) => t.id === Number(id));

  const [beforePhotos, setBeforePhotos] = useState<string[]>([]);
  const [afterPhotos, setAfterPhotos] = useState<string[]>([]);
  const [rating, setRating] = useState(0);
  const [materials, setMaterials] = useState<string[]>(["RO Membrane ×1", "Sediment Filter ×2"]);
  const sigRef = useRef<SignaturePadHandle>(null);

  if (!task) {
    return (
      <div className="flex flex-1 flex-col items-center justify-center gap-3 px-6 text-center">
        <p className="text-sm font-semibold text-slate-500">Task not found.</p>
        <Button variant="secondary" onClick={() => router.push("/tasks")}>
          Back to tasks
        </Button>
      </div>
    );
  }

  const step = task.step;
  const isComplete = step >= 4;

  function addMaterial() {
    const value = window.prompt('Add material used (e.g. "Carbon Filter ×1")');
    if (value && value.trim()) {
      setMaterials((prev) => [...prev, value.trim()]);
    }
  }

  return (
    <div className="flex flex-1 flex-col">
      <div className="sticky top-0 z-10 flex items-center gap-3 border-b border-slate-100 bg-white/95 px-4 py-3 backdrop-blur">
        <button
          onClick={() => router.push("/tasks")}
          className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full hover:bg-slate-100"
        >
          <ArrowLeft className="h-5 w-5 text-slate-600" strokeWidth={1.75} />
        </button>
        <div>
          <p className="text-xs font-semibold text-slate-400">{task.taskNo}</p>
          <p className="text-sm font-bold text-slate-800">Task details</p>
        </div>
      </div>

      <div className="px-5 py-5">
        <Stepper step={step} />

        <Card className="mb-4 p-4">
          <div className="mb-3 flex items-center justify-between gap-2">
            <p className="font-display text-base font-bold text-slate-800">{task.name}</p>
            <Badge variant={badgeVariantForType(task.type)} className="shrink-0">
              {task.type}
            </Badge>
          </div>
          <div className="mb-2 flex items-center gap-2 text-sm text-slate-500">
            <MapPin className="h-4 w-4 shrink-0 text-slate-400" strokeWidth={1.75} />
            <span>{task.address}</span>
          </div>
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <Phone className="h-4 w-4 shrink-0 text-slate-400" strokeWidth={1.75} />
              <span>{task.phone}</span>
            </div>
            <a
              href={`tel:${task.phone.replace(/\s+/g, "")}`}
              className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-aqua-50"
            >
              <Phone className="h-4 w-4 text-aqua-600" strokeWidth={1.75} />
            </a>
          </div>
        </Card>

        <div className="mb-5">
          <FieldLabel htmlFor="workDesc">Work description</FieldLabel>
          <FieldTextarea
            id="workDesc"
            rows={3}
            defaultValue="RO membrane replacement and full system service. Water quality tested — TDS reduced from 180 to 12 ppm."
          />
        </div>

        <div className="mb-5">
          <FieldLabel>Materials used</FieldLabel>
          <div className="flex flex-wrap gap-2">
            {materials.map((m, i) => (
              <Badge key={i} variant="neutral">
                {m}
              </Badge>
            ))}
            <button
              type="button"
              onClick={addMaterial}
              className="inline-flex items-center gap-1 rounded-full border border-dashed border-slate-300 bg-white px-2.5 py-1 text-[10.5px] font-bold uppercase tracking-wide text-slate-400"
            >
              + Add
            </button>
          </div>
        </div>

        <div className="mb-5">
          <FieldLabel>Before photos</FieldLabel>
          <PhotoUploadGrid
            photos={beforePhotos}
            onAdd={(url) => setBeforePhotos((prev) => [...prev, url])}
          />
        </div>

        <div className="mb-5">
          <FieldLabel>After photos</FieldLabel>
          <PhotoUploadGrid
            photos={afterPhotos}
            onAdd={(url) => setAfterPhotos((prev) => [...prev, url])}
          />
        </div>

        <div className="mb-5">
          <div className="mb-1.5 flex items-center justify-between">
            <p className="mb-0 flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-500">
              <Pencil className="h-3.5 w-3.5" strokeWidth={1.75} /> Customer signature
            </p>
            <button
              type="button"
              onClick={() => sigRef.current?.clear()}
              className="text-[11px] font-bold text-aqua-600"
            >
              Clear
            </button>
          </div>
          <SignaturePad ref={sigRef} />
        </div>

        <div className="mb-6">
          <FieldLabel>Customer rating</FieldLabel>
          <StarRating value={rating} onChange={setRating} />
        </div>

        <Button
          block
          variant={isComplete ? "secondary" : "primary"}
          disabled={isComplete}
          onClick={() => advanceTaskStep(task.id)}
        >
          {isComplete ? "✓ Job Completed" : STEP_ACTIONS[step]}
        </Button>
      </div>
    </div>
  );
}
