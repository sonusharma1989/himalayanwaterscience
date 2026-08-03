"use client";

import { createContext, useCallback, useContext, useState, ReactNode } from "react";
import { Task, AttendanceState } from "./types";
import { INITIAL_TASKS } from "./data";

interface AppContextValue {
  tasks: Task[];
  advanceTaskStep: (id: number) => void;
  attendance: AttendanceState;
  toggleAttendance: () => void;
}

const AppContext = createContext<AppContextValue | null>(null);

export function AppProvider({ children }: { children: ReactNode }) {
  const [tasks, setTasks] = useState<Task[]>(INITIAL_TASKS);
  const [attendance, setAttendance] = useState<AttendanceState>({
    checkedIn: false,
    checkInTime: null,
    checkOutTime: null,
  });

  const advanceTaskStep = useCallback((id: number) => {
    setTasks((prev) =>
      prev.map((t) => (t.id === id && t.step < 4 ? { ...t, step: t.step + 1 } : t))
    );
  }, []);

  const toggleAttendance = useCallback(() => {
    const now = new Date().toLocaleTimeString("en-IN", {
      hour: "2-digit",
      minute: "2-digit",
      hour12: true,
    });
    setAttendance((prev) =>
      prev.checkedIn
        ? { ...prev, checkedIn: false, checkOutTime: now }
        : { ...prev, checkedIn: true, checkInTime: now }
    );
  }, []);

  return (
    <AppContext.Provider value={{ tasks, advanceTaskStep, attendance, toggleAttendance }}>
      {children}
    </AppContext.Provider>
  );
}

export function useApp() {
  const ctx = useContext(AppContext);
  if (!ctx) throw new Error("useApp must be used within an AppProvider");
  return ctx;
}
