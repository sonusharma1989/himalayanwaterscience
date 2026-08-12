"use client";

import { useEffect, useRef, useState } from "react";
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
  const { tasks, advanceTaskStep, loading } = useApp();
  const task = tasks.find((t) => t.id === Number(id));

  const [beforePhotos, setBeforePhotos] = useState<string[]>([]);
  const [afterPhotos, setAfterPhotos] = useState<string[]>([]);
  const [rating, setRating] = useState(0);
  const [materials, setMaterials] = useState<string[]>([]);
  const [newMaterial, setNewMaterial] = useState("");
  const [workDesc, setWorkDesc] = useState(
    "RO membrane replacement and full system service. Water quality tested — TDS reduced from 180 to 12 ppm."
  );
  const sigRef = useRef<SignaturePadHandle>(null);

  // Sync state values when task finishes loading
  useEffect(() => {
    if (task) {
      if (task.isSurvey) {
        router.replace("/survey");
        return;
      }
      setBeforePhotos(task.beforePhotos || []);
      setAfterPhotos(task.afterPhotos || []);
      setRating(task.rating || 0);
      setMaterials(task.materials || []);
      if (task.workDescription) {
        setWorkDesc(task.workDescription);
      }
    }
  }, [task, router]);

  if (loading) {
    return (
      <div className="flex flex-1 flex-col items-center justify-center gap-3 px-6 py-20 text-center">
        <svg className="animate-spin h-8 w-8 text-aqua-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
          <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p className="text-sm font-semibold text-slate-500">Loading task details...</p>
      </div>
    );
  }

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

  async function handleActionClick() {
    if (!task) return;
    if (step === 3) {
      // Completed step (step 3 -> 4): send full details
      const signature = sigRef.current?.toDataURL() || undefined;
      await advanceTaskStep(task.id, {
        beforePhotos,
        afterPhotos,
        workDescription: workDesc,
        materials,
        rating,
        signature,
      });
    } else {
      await advanceTaskStep(task.id);
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
            value={workDesc}
            onChange={(e) => setWorkDesc(e.target.value)}
            disabled={isComplete}
          />
        </div>

        <div className="mb-5">
          <FieldLabel>Materials used</FieldLabel>
          <div className="flex flex-wrap gap-2 mb-2">
            {materials.length === 0 ? (
              <span className="text-xs text-slate-400">No materials added yet.</span>
            ) : (
              materials.map((m, i) => (
                <Badge key={i} variant="neutral">
                  {m}
                </Badge>
              ))
            )}
          </div>
          {!isComplete && (
            <div className="flex gap-2">
              <input
                type="text"
                placeholder="e.g. Carbon Filter ×1"
                value={newMaterial}
                onChange={(e) => setNewMaterial(e.target.value)}
                className="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-aqua-500 focus:bg-white focus:outline-none"
              />
              <button
                type="button"
                onClick={() => {
                  if (newMaterial.trim()) {
                    setMaterials((prev) => [...prev, newMaterial.trim()]);
                    setNewMaterial("");
                  }
                }}
                className="rounded-xl bg-aqua-600 px-4 py-2 text-xs font-bold text-white hover:bg-aqua-700"
              >
                Add
              </button>
            </div>
          )}
        </div>

        <div className="mb-5">
          <FieldLabel>Before photos</FieldLabel>
          <PhotoUploadGrid
            photos={beforePhotos}
            onAdd={(url) => setBeforePhotos((prev) => [...prev, url])}
            readOnly={isComplete}
          />
        </div>

        <div className="mb-5">
          <FieldLabel>After photos</FieldLabel>
          <PhotoUploadGrid
            photos={afterPhotos}
            onAdd={(url) => setAfterPhotos((prev) => [...prev, url])}
            readOnly={isComplete}
          />
        </div>

        {!isComplete && (
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
        )}

        <div className="mb-6">
          <FieldLabel>Customer rating</FieldLabel>
          <StarRating value={rating} onChange={(val) => !isComplete && setRating(val)} />
        </div>

        <Button
          block
          variant={isComplete ? "secondary" : "primary"}
          disabled={isComplete}
          onClick={handleActionClick}
        >
          {isComplete ? "✓ Job Completed" : STEP_ACTIONS[step]}
        </Button>
      </div>
    </div>
  );
}
