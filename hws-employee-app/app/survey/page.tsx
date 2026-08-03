"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { ArrowLeft, MapPin } from "lucide-react";
import { useApp } from "@/lib/store";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { FieldLabel, FieldInput, FieldTextarea } from "@/components/ui/Field";
import { PhotoUploadGrid } from "@/components/PhotoUploadGrid";
import { ChipGroup } from "@/components/ChipGroup";

export default function SurveyPage() {
  const router = useRouter();
  const { tasks } = useApp();
  const surveyTask = tasks.find((t) => t.isSurvey);

  const [propertyType, setPropertyType] = useState(["Hotel"]);
  const [waterSource, setWaterSource] = useState(["Municipal"]);
  const [wastewaterDisposal, setWastewaterDisposal] = useState(["Septic tank"]);
  const [inquiryTypes, setInquiryTypes] = useState(["STP"]);
  const [spaceAvailable, setSpaceAvailable] = useState(["Yes — open area"]);
  const [photos, setPhotos] = useState<string[]>([]);
  const [submitted, setSubmitted] = useState(false);
  const [draftSaved, setDraftSaved] = useState(false);

  function handleSaveDraft() {
    setDraftSaved(true);
    setTimeout(() => setDraftSaved(false), 2000);
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
          <p className="text-xs font-semibold text-slate-400">{surveyTask?.taskNo ?? "SRV-0512"}</p>
          <p className="text-sm font-bold text-slate-800">Site survey</p>
        </div>
      </div>

      <div className="px-5 py-5">
        <div className="mb-5">
          <FieldLabel>Property type</FieldLabel>
          <ChipGroup
            options={["Hotel", "Hospital", "Bungalow", "Other"]}
            value={propertyType}
            onChange={setPropertyType}
          />
        </div>

        <div className="mb-5 space-y-4">
          <div>
            <FieldLabel htmlFor="svName">Property / business name</FieldLabel>
            <FieldInput id="svName" defaultValue={surveyTask?.name ?? ""} />
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <FieldLabel htmlFor="svOwner">Owner / contact person</FieldLabel>
              <FieldInput id="svOwner" defaultValue={surveyTask?.owner ?? ""} />
            </div>
            <div>
              <FieldLabel htmlFor="svPhone">Contact number</FieldLabel>
              <FieldInput id="svPhone" defaultValue={surveyTask?.phone ?? ""} />
            </div>
          </div>
          <div>
            <FieldLabel htmlFor="svAddress">Address</FieldLabel>
            <FieldTextarea id="svAddress" rows={2} defaultValue={surveyTask?.address ?? ""} />
          </div>
        </div>

        <Card className="mb-5 p-4">
          <div className="mb-3 flex items-center gap-2">
            <MapPin className="h-4 w-4 text-slate-400" strokeWidth={1.75} />
            <p className="text-xs font-bold text-slate-600">Location</p>
          </div>
          <div className="relative flex h-24 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
            <div
              className="absolute inset-0 opacity-40"
              style={{
                backgroundImage: "radial-gradient(circle,#94a3b8 1px,transparent 1px)",
                backgroundSize: "14px 14px",
              }}
            />
            <MapPin className="relative z-10 h-7 w-7 text-rose-500" strokeWidth={1.75} />
          </div>
          <p className="mt-2 text-[11px] font-medium text-slate-400">
            30.3268° N, 78.0421° E · captured on arrival
          </p>
        </Card>

        <div className="mb-5">
          <FieldLabel>Site photos</FieldLabel>
          <PhotoUploadGrid photos={photos} onAdd={(url) => setPhotos((prev) => [...prev, url])} />
        </div>

        <p className="mb-3 text-xs font-bold text-slate-600">Building &amp; structure details</p>
        <div className="mb-3 grid grid-cols-2 gap-3">
          <div>
            <FieldLabel htmlFor="floors">Floors</FieldLabel>
            <FieldInput id="floors" type="number" placeholder="e.g. 4" />
          </div>
          <div>
            <FieldLabel htmlFor="area">Built-up area (sq.ft)</FieldLabel>
            <FieldInput id="area" type="number" placeholder="e.g. 12000" />
          </div>
        </div>
        <div className="mb-4 grid grid-cols-2 gap-3">
          <div>
            <FieldLabel htmlFor="units">Rooms / beds / units</FieldLabel>
            <FieldInput id="units" type="number" placeholder="e.g. 60" />
          </div>
          <div>
            <FieldLabel htmlFor="waterUse">Est. water use (KLD)</FieldLabel>
            <FieldInput id="waterUse" type="number" placeholder="e.g. 25" />
          </div>
        </div>

        <div className="mb-4">
          <FieldLabel>Current water source</FieldLabel>
          <ChipGroup
            options={["Municipal", "Borewell", "Tanker", "River"]}
            value={waterSource}
            onChange={setWaterSource}
          />
        </div>

        <div className="mb-5">
          <FieldLabel>Current wastewater disposal</FieldLabel>
          <ChipGroup
            options={["Septic tank", "Open drain", "Existing STP", "None"]}
            value={wastewaterDisposal}
            onChange={setWastewaterDisposal}
          />
        </div>

        <div className="mb-5">
          <FieldLabel>
            Inquiry type{" "}
            <span className="text-slate-300 normal-case font-medium">— select all that apply</span>
          </FieldLabel>
          <ChipGroup
            options={["STP", "WTP", "ETP", "RO Plant", "Softener", "AMC only"]}
            multi
            value={inquiryTypes}
            onChange={setInquiryTypes}
          />
        </div>

        <div className="mb-5">
          <FieldLabel>Space available for plant</FieldLabel>
          <ChipGroup
            options={["Yes — open area", "Limited", "Basement only", "Not sure"]}
            value={spaceAvailable}
            onChange={setSpaceAvailable}
          />
        </div>

        <div className="mb-5">
          <FieldLabel htmlFor="notes">Notes for sales team</FieldLabel>
          <FieldTextarea
            id="notes"
            rows={3}
            placeholder="Anything the quotation team should know..."
            defaultValue="Interested in replacing a 12-year-old STP. Decision maker available after 4pm."
          />
        </div>

        <div className="mb-6">
          <FieldLabel htmlFor="followUp">Follow-up date</FieldLabel>
          <FieldInput id="followUp" type="date" defaultValue="2026-08-09" />
        </div>

        <div className="flex items-center gap-2.5">
          <Button variant="secondary" className="flex-1" onClick={handleSaveDraft}>
            {draftSaved ? "✓ Saved" : "Save Draft"}
          </Button>
          <Button
            className="flex-1"
            variant={submitted ? "secondary" : "primary"}
            disabled={submitted}
            onClick={() => setSubmitted(true)}
          >
            {submitted ? "✓ Survey Submitted" : "Submit Survey"}
          </Button>
        </div>
      </div>
    </div>
  );
}
