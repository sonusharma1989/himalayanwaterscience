import { Task } from "./types";
import { BadgeVariant } from "@/components/ui/Badge";

export const STEP_LABELS = ["Assign", "Accept", "Travel", "Work", "Done"] as const;

export const STEP_ACTIONS = [
  "Accept Job",
  "Start Travel",
  "Reached — Start Work",
  "Mark Job Complete",
  "Completed",
] as const;

export const INITIAL_TASKS: Task[] = [
  {
    id: 1,
    name: "Rajesh Bhatt",
    phone: "+91 98765 43210",
    address: "12 Rajpur Road, Dehradun",
    type: "Installation",
    time: "10:30 AM",
    priority: "high",
    step: 0,
    taskNo: "HWS-2381",
  },
  {
    id: 2,
    name: "Meena Rawat",
    phone: "+91 91234 56780",
    address: "Shastri Nagar, Rishikesh",
    type: "AMC Service",
    time: "12:00 PM",
    priority: "normal",
    step: 1,
    taskNo: "HWS-2382",
  },
  {
    id: 3,
    name: "Karan Negi",
    phone: "+91 99887 76655",
    address: "Clock Tower, Haridwar",
    type: "Complaint",
    time: "2:15 PM",
    priority: "urgent",
    step: 2,
    taskNo: "HWS-2383",
  },
  {
    id: 4,
    name: "Sunita Panwar",
    phone: "+91 98123 45670",
    address: "Vasant Vihar, Dehradun",
    type: "Service",
    time: "4:00 PM",
    priority: "normal",
    step: 4,
    taskNo: "HWS-2384",
  },
  {
    id: 5,
    name: "Anil Thapliyal",
    phone: "+91 97654 32109",
    address: "Mussoorie Road, Dehradun",
    type: "Sales Visit",
    time: "5:30 PM",
    priority: "low",
    step: 0,
    taskNo: "HWS-2385",
  },
  {
    id: 6,
    name: "Grand Himalaya Resort",
    owner: "Vikram Chauhan",
    phone: "+91 94120 55678",
    address: "Rajpur Road, near Clock Tower, Dehradun",
    type: "Site Survey",
    time: "11:00 AM",
    priority: "high",
    step: 0,
    taskNo: "SRV-0512",
    isSurvey: true,
  },
];

export function statusCategory(step: number): "pending" | "progress" | "done" {
  if (step >= 4) return "done";
  if (step === 0) return "pending";
  return "progress";
}

export function priorityBarClass(p: Task["priority"]): string {
  if (p === "urgent") return "bg-rose-500";
  if (p === "high") return "bg-amber-500";
  return "bg-slate-200";
}

export function badgeVariantForType(type: Task["type"]): BadgeVariant {
  switch (type) {
    case "Installation":
      return "install";
    case "AMC Service":
      return "amc";
    case "Complaint":
      return "complaint";
    case "Service":
      return "service";
    case "Sales Visit":
      return "sales";
    case "Site Survey":
      return "survey";
    default:
      return "service";
  }
}
