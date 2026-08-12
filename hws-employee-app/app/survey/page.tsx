"use client";

import { useEffect, useState } from "react";
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
  const { tasks, submitSurvey } = useApp();
  const [taskId, setTaskId] = useState<string | null>(null);

  useEffect(() => {
    if (typeof window !== "undefined") {
      const params = new URLSearchParams(window.location.search);
      setTaskId(params.get("taskId"));
    }
  }, []);

  const surveyTask = taskId ? tasks.find((t) => t.id === Number(taskId)) : undefined;

  const [propertyType, setPropertyType] = useState(["Hotel"]);
  const [waterSource, setWaterSource] = useState(["Municipal"]);
  const [wastewaterDisposal, setWastewaterDisposal] = useState(["Septic tank"]);
  const [inquiryTypes, setInquiryTypes] = useState(["STP"]);
  const [spaceAvailable, setSpaceAvailable] = useState(["Yes — open area"]);
  const [photos, setPhotos] = useState<string[]>([]);
  
  const [name, setName] = useState("");
  const [owner, setOwner] = useState("");
  const [phone, setPhone] = useState("");
  const [address, setAddress] = useState("");

  const [floors, setFloors] = useState("");
  const [builtUpAreaSqft, setBuiltUpAreaSqft] = useState("");
  const [roomsUnits, setRoomsUnits] = useState("");
  const [waterUseKld, setWaterUseKld] = useState("");
  const [notes, setNotes] = useState("Interested in replacing a 12-year-old STP. Decision maker available after 4pm.");
  const [followUpDate, setFollowUpDate] = useState("2026-08-09");

  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);
  const [draftSaved, setDraftSaved] = useState(false);
  const [coords, setCoords] = useState<{ latitude: number; longitude: number } | null>(null);

  // Fetch device geolocation on mount
  useEffect(() => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          setCoords({
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
          });
        },
        () => {
          // Fallback to Dehradun coordinates if blocked
          setCoords({ latitude: 30.3268, longitude: 78.0421 });
        }
      );
    } else {
      setCoords({ latitude: 30.3268, longitude: 78.0421 });
    }
  }, []);

  const [surveyId, setSurveyId] = useState<number>(0);

  useEffect(() => {
    if (surveyTask) {
      setName(surveyTask.name || "");
      setOwner(surveyTask.owner || "");
      setPhone(surveyTask.phone || "");
      setAddress(surveyTask.address || "");
      if (surveyTask.surveyPhotos) {
        setPhotos(surveyTask.surveyPhotos);
      }
      if (surveyTask.step >= 4) {
        setSubmitted(true);
      }
    } else {
      // Try to load draft from localStorage for independent surveys
      const draftStr = localStorage.getItem("hws_survey_draft");
      if (draftStr) {
        try {
          const draft = JSON.parse(draftStr);
          if (draft.surveyId) setSurveyId(draft.surveyId);
          if (draft.name) setName(draft.name);
          if (draft.owner) setOwner(draft.owner);
          if (draft.phone) setPhone(draft.phone);
          if (draft.address) setAddress(draft.address);
          if (draft.propertyType) setPropertyType(draft.propertyType);
          if (draft.waterSource) setWaterSource(draft.waterSource);
          if (draft.wastewaterDisposal) setWastewaterDisposal(draft.wastewaterDisposal);
          if (draft.inquiryTypes) setInquiryTypes(draft.inquiryTypes);
          if (draft.spaceAvailable) setSpaceAvailable(draft.spaceAvailable);
          if (draft.floors) setFloors(draft.floors);
          if (draft.builtUpAreaSqft) setBuiltUpAreaSqft(draft.builtUpAreaSqft);
          if (draft.roomsUnits) setRoomsUnits(draft.roomsUnits);
          if (draft.waterUseKld) setWaterUseKld(draft.waterUseKld);
          if (draft.notes) setNotes(draft.notes);
          if (draft.followUpDate) setFollowUpDate(draft.followUpDate);
          if (draft.photos) setPhotos(draft.photos);
        } catch (e) {}
      }
    }
  }, [surveyTask]);

  async function handleSaveDraft() {
    setLoading(true);
    // 1. Save to database as draft status
    const resultSurveyId = await submitSurvey(surveyTask?.id || surveyId, {
      propertyType,
      waterSource,
      wastewaterDisposal,
      inquiryTypes,
      spaceAvailable,
      floors,
      builtUpAreaSqft,
      roomsUnits,
      waterUseKld,
      notes,
      followUpDate,
      photos,
      latitude: coords?.latitude,
      longitude: coords?.longitude,
      customer_name: name,
      customer_phone: phone,
      customer_address: address,
    }, "draft");

    setLoading(false);

    // 2. Cache locally as fallback/draft structure
    const draftData = {
      surveyId: resultSurveyId || surveyId,
      name,
      owner,
      phone,
      address,
      propertyType,
      waterSource,
      wastewaterDisposal,
      inquiryTypes,
      spaceAvailable,
      floors,
      builtUpAreaSqft,
      roomsUnits,
      waterUseKld,
      notes,
      followUpDate,
      photos,
    };
    if (resultSurveyId) {
      setSurveyId(resultSurveyId);
    }
    localStorage.setItem("hws_survey_draft", JSON.stringify(draftData));
    setDraftSaved(true);
    setTimeout(() => setDraftSaved(false), 2000);
  }

  async function handleSubmit() {
    setLoading(true);
    const successId = await submitSurvey(surveyTask?.id || surveyId, {
      propertyType,
      waterSource,
      wastewaterDisposal,
      inquiryTypes,
      spaceAvailable,
      floors,
      builtUpAreaSqft,
      roomsUnits,
      waterUseKld,
      notes,
      followUpDate,
      photos,
      latitude: coords?.latitude,
      longitude: coords?.longitude,
      customer_name: name,
      customer_phone: phone,
      customer_address: address,
    }, "submitted");
    setLoading(false);
    if (successId) {
      localStorage.removeItem("hws_survey_draft");
      setSubmitted(true);
      setTimeout(() => {
        router.push("/tasks");
      }, 1500);
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
            <FieldInput
              id="svName"
              value={name}
              onChange={(e) => setName(e.target.value)}
              disabled={submitted}
            />
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <FieldLabel htmlFor="svOwner">Owner / contact person</FieldLabel>
              <FieldInput
                id="svOwner"
                value={owner}
                onChange={(e) => setOwner(e.target.value)}
                disabled={submitted}
              />
            </div>
            <div>
              <FieldLabel htmlFor="svPhone">Contact number</FieldLabel>
              <FieldInput
                id="svPhone"
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
                disabled={submitted}
              />
            </div>
          </div>
          <div>
            <FieldLabel htmlFor="svAddress">Address</FieldLabel>
            <FieldTextarea
              id="svAddress"
              rows={2}
              value={address}
              onChange={(e) => setAddress(e.target.value)}
              disabled={submitted}
            />
          </div>
        </div>

        <Card className="mb-5 p-4">
          <div className="mb-3 flex items-center gap-2">
            <MapPin className="h-4 w-4 text-slate-400" strokeWidth={1.75} />
            <p className="text-xs font-bold text-slate-600">Location</p>
          </div>
          <div className="relative flex h-36 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
            {coords ? (
              <iframe
                src={`https://maps.google.com/maps?q=${coords.latitude},${coords.longitude}&t=&z=15&ie=UTF8&iwloc=&output=embed`}
                width="100%"
                height="100%"
                style={{ border: 0 }}
                allowFullScreen={false}
                loading="lazy"
              />
            ) : (
              <div className="text-xs text-slate-400 flex items-center gap-2">
                <svg className="animate-spin h-4 w-4 text-aqua-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Getting location coordinates...
              </div>
            )}
          </div>
          <p className="mt-2 text-[11px] font-medium text-slate-400">
            {coords ? `${coords.latitude.toFixed(4)}° N, ${coords.longitude.toFixed(4)}° E · Live location` : "Rajpur Road, Dehradun"}
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
            <FieldInput
              id="floors"
              type="number"
              placeholder="e.g. 4"
              value={floors}
              onChange={(e) => setFloors(e.target.value)}
            />
          </div>
          <div>
            <FieldLabel htmlFor="area">Built-up area (sq.ft)</FieldLabel>
            <FieldInput
              id="area"
              type="number"
              placeholder="e.g. 12000"
              value={builtUpAreaSqft}
              onChange={(e) => setBuiltUpAreaSqft(e.target.value)}
            />
          </div>
        </div>
        <div className="mb-4 grid grid-cols-2 gap-3">
          <div>
            <FieldLabel htmlFor="units">Rooms / beds / units</FieldLabel>
            <FieldInput
              id="units"
              type="number"
              placeholder="e.g. 60"
              value={roomsUnits}
              onChange={(e) => setRoomsUnits(e.target.value)}
            />
          </div>
          <div>
            <FieldLabel htmlFor="waterUse">Est. water use (KLD)</FieldLabel>
            <FieldInput
              id="waterUse"
              type="number"
              placeholder="e.g. 25"
              value={waterUseKld}
              onChange={(e) => setWaterUseKld(e.target.value)}
            />
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
            value={notes}
            onChange={(e) => setNotes(e.target.value)}
          />
        </div>

        <div className="mb-6">
          <FieldLabel htmlFor="followUp">Follow-up date</FieldLabel>
          <FieldInput
            id="followUp"
            type="date"
            value={followUpDate}
            onChange={(e) => setFollowUpDate(e.target.value)}
          />
        </div>

        <div className="flex items-center gap-2.5">
          <Button variant="secondary" className="flex-1" onClick={handleSaveDraft} disabled={loading || submitted}>
            {draftSaved ? "✓ Saved" : "Save Draft"}
          </Button>
          <Button
            className="flex-1"
            variant={submitted ? "secondary" : "primary"}
            disabled={submitted || loading}
            onClick={handleSubmit}
          >
            {submitted ? "✓ Submitted" : loading ? "Submitting..." : "Submit Survey"}
          </Button>
        </div>
      </div>
    </div>
  );
}
