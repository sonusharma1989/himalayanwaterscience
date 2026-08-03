export type TaskType =
  | "Installation"
  | "AMC Service"
  | "Complaint"
  | "Service"
  | "Sales Visit"
  | "Site Survey";

export type Priority = "urgent" | "high" | "normal" | "low";

export type TaskStatus = "pending" | "progress" | "done";

export interface Task {
  id: number;
  name: string;
  owner?: string;
  phone: string;
  address: string;
  type: TaskType;
  time: string;
  priority: Priority;
  step: number; // 0 = Assign, 1 = Accept, 2 = Travel, 3 = Work, 4 = Done
  taskNo: string;
  isSurvey?: boolean;
}

export interface AttendanceState {
  checkedIn: boolean;
  checkInTime: string | null;
  checkOutTime: string | null;
}

export type TaskFilter = "all" | TaskStatus;
