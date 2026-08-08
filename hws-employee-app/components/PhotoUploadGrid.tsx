"use client";

import { ChangeEvent } from "react";
import { Camera } from "lucide-react";

interface PhotoUploadGridProps {
  photos: string[];
  onAdd: (dataUrl: string) => void;
  readOnly?: boolean;
}

export function PhotoUploadGrid({ photos, onAdd, readOnly = false }: PhotoUploadGridProps) {
  function handleChange(e: ChangeEvent<HTMLInputElement>) {
    if (readOnly) return;
    const files = e.target.files;
    if (!files) return;
    Array.from(files).forEach((file) => {
      const reader = new FileReader();
      reader.onload = (ev) => {
        if (typeof ev.target?.result === "string") {
          onAdd(ev.target.result);
        }
      };
      reader.readAsDataURL(file);
    });
    e.target.value = "";
  }

  return (
    <div className="grid grid-cols-4 gap-2">
      {photos.map((src, i) => (
        // eslint-disable-next-line @next/next/no-img-element
        <div key={i} className="aspect-square overflow-hidden rounded-xl border border-slate-200">
          <img src={src} alt="Uploaded" className="h-full w-full object-cover" />
        </div>
      ))}
      {!readOnly && (
        <label className="flex aspect-square cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-slate-300 transition-colors hover:border-aqua-400 hover:bg-aqua-50/50">
          <Camera className="h-5 w-5 text-slate-400" strokeWidth={1.75} />
          <span className="text-[9px] font-bold text-slate-400">ADD</span>
          <input
            type="file"
            accept="image/*"
            multiple
            className="hidden"
            onChange={handleChange}
          />
        </label>
      )}
    </div>
  );
}
