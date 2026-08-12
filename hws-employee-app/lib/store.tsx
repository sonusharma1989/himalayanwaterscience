"use client";

import { createContext, useCallback, useContext, useState, useEffect, ReactNode } from "react";
import { Task, AttendanceState, AppNotification } from "./types";

const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8080/api/v1/employee";

interface UserProfile {
  id: number;
  name: string;
  email: string;
  role: string | null;
  stats?: {
    jobs: number;
    rating: number;
    attendance: number;
  };
}

interface AppContextValue {
  user: UserProfile | null;
  token: string | null;
  tasks: Task[];
  attendance: AttendanceState;
  notifications: AppNotification[];
  unreadCount: number;
  loading: boolean;
  error: string | null;
  login: (email: string, password: string) => Promise<boolean>;
  logout: () => void;
  fetchTasks: () => Promise<void>;
  advanceTaskStep: (
    id: number,
    details?: {
      beforePhotos?: string[];
      afterPhotos?: string[];
      workDescription?: string;
      materials?: string[];
      rating?: number;
      signature?: string;
    }
  ) => Promise<void>;
  fetchAttendance: () => Promise<void>;
  toggleAttendance: (selfieFile?: File) => Promise<void>;
  submitSurvey: (id: number, data: any) => Promise<boolean>;
  fetchNotifications: () => Promise<void>;
  markNotificationsRead: (id?: number) => Promise<void>;
}

const AppContext = createContext<AppContextValue | null>(null);

export function AppProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<UserProfile | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [tasks, setTasks] = useState<Task[]>([]);
  const [attendance, setAttendance] = useState<AttendanceState>({
    checkedIn: false,
    checkInTime: null,
    checkOutTime: null,
  });
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);
  const [notifications, setNotifications] = useState<AppNotification[]>([]);
  const [unreadCount, setUnreadCount] = useState<number>(0);

  // Restore session from localStorage on mount
  useEffect(() => {
    const savedToken = localStorage.getItem("hws_token");
    const savedUser = localStorage.getItem("hws_user");
    if (savedToken && savedUser) {
      setToken(savedToken);
      setUser(JSON.parse(savedUser));
    }
  }, []);

  // Fetch initial data when authenticated
  useEffect(() => {
    if (token) {
      const loadInitialData = async () => {
        setLoading(true);
        await Promise.all([fetchTasks(), fetchAttendance(), fetchNotifications()]);
        setLoading(false);
      };
      loadInitialData();
    }
  }, [token]);

  const login = async (email: string, password: string): Promise<boolean> => {
    setLoading(true);
    setError(null);
    try {
      const res = await fetch(`${API_URL}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });
      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || "Login failed.");
      }
      setToken(data.token);
      setUser(data.user);
      localStorage.setItem("hws_token", data.token);
      localStorage.setItem("hws_user", JSON.stringify(data.user));
      setLoading(false);
      return true;
    } catch (err: any) {
      setError(err.message);
      setLoading(false);
      return false;
    }
  };

  const logout = () => {
    if (token) {
      fetch(`${API_URL}/logout`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
      }).catch(() => {});
    }
    setToken(null);
    setUser(null);
    setTasks([]);
    setAttendance({ checkedIn: false, checkInTime: null, checkOutTime: null });
    setNotifications([]);
    setUnreadCount(0);
    localStorage.removeItem("hws_token");
    localStorage.removeItem("hws_user");
  };

  const fetchTasks = async () => {
    if (!token) return;
    try {
      const res = await fetch(`${API_URL}/tasks`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      const data = await res.json();
      if (res.ok) {
        setTasks(data);
      }
    } catch (err) {}
  };

  const advanceTaskStep = async (
    id: number,
    details?: {
      beforePhotos?: string[];
      afterPhotos?: string[];
      workDescription?: string;
      materials?: string[];
      rating?: number;
      signature?: string;
    }
  ) => {
    if (!token) return;
    const task = tasks.find((t) => t.id === id);
    if (!task || task.step >= 4) return;
    const nextStep = task.step + 1;

    try {
      const payload = {
        step: nextStep,
        work_description: details?.workDescription,
        rating: details?.rating,
        materials: details?.materials,
        before_photos: details?.beforePhotos,
        after_photos: details?.afterPhotos,
        signature: details?.signature,
      };

      const res = await fetch(`${API_URL}/tasks/${id}/step`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (res.ok) {
        setTasks((prev) =>
          prev.map((t) =>
            t.id === id
              ? {
                  ...t,
                  step: nextStep,
                  workDescription: data.task.workDescription,
                  rating: data.task.rating,
                  materials: data.task.materials,
                  beforePhotos: data.task.beforePhotos,
                  afterPhotos: data.task.afterPhotos,
                }
              : t
          )
        );
      }
    } catch (err) {}
  };

  const fetchAttendance = async () => {
    if (!token) return;
    try {
      const res = await fetch(`${API_URL}/attendance/today`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      const data = await res.json();
      if (res.ok) {
        setAttendance(data);
      }
    } catch (err) {}
  };

  const toggleAttendance = async (selfieFile?: File) => {
    if (!token) return;
    const method = attendance.checkedIn ? "check-out" : "check-in";

    try {
      // Fetch mock GPS location coords
      const latitude = 30.3165;
      const longitude = 78.0322;

      const formData = new FormData();
      formData.append("latitude", latitude.toString());
      formData.append("longitude", longitude.toString());
      if (selfieFile) {
        formData.append("selfie", selfieFile);
      }

      const res = await fetch(`${API_URL}/attendance/${method}`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
        },
        body: formData,
      });
      const data = await res.json();
      if (res.ok) {
        setAttendance(data);
      }
    } catch (err) {}
  };

  const submitSurvey = async (id: number, surveyData: any): Promise<boolean> => {
    if (!token) return false;

    // Convert frontend string chips/values to DB enum strings
    const dbPropertyType = surveyData.propertyType?.[0]?.toLowerCase() || "other";
    const dbWaterSource = surveyData.waterSource?.[0]?.toLowerCase() || "municipal";
    const dbWastewaterDisposal = surveyData.wastewaterDisposal?.[0]?.toLowerCase()?.replace(" ", "_") || "none";
    
    let dbSpaceAvailable = "not_sure";
    if (surveyData.spaceAvailable?.[0]) {
      const space = surveyData.spaceAvailable[0].toLowerCase();
      if (space.includes("open")) dbSpaceAvailable = "open_area";
      else if (space.includes("limit")) dbSpaceAvailable = "limited";
      else if (space.includes("basement")) dbSpaceAvailable = "basement_only";
    }

    const dbInquiryTypes = (surveyData.inquiryTypes || []).map((type: string) => 
      type.toLowerCase().replace(" ", "_")
    );

    const formData = {
      property_type: dbPropertyType,
      floors: parseInt(surveyData.floors) || null,
      built_up_area_sqft: parseInt(surveyData.builtUpAreaSqft) || null,
      rooms_units: parseInt(surveyData.roomsUnits) || null,
      water_use_kld: parseFloat(surveyData.waterUseKld) || null,
      water_source: dbWaterSource,
      wastewater_disposal: dbWastewaterDisposal,
      space_available: dbSpaceAvailable,
      notes: surveyData.notes || "",
      follow_up_date: surveyData.followUpDate || null,
      latitude: 30.3268,
      longitude: 78.0421,
      inquiry_types: dbInquiryTypes,
      photos: surveyData.photos || [],
    };

    try {
      const res = await fetch(`${API_URL}/tasks/${id}/survey`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      if (res.ok) {
        await fetchTasks(); // Refresh list to reflect step 4 status
        return true;
      }
      return false;
    } catch (err) {
      return false;
    }
  };

  const fetchNotifications = async () => {
    if (!token) return;
    try {
      const res = await fetch(`${API_URL}/notifications`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      const data = await res.json();
      if (res.ok) {
        setNotifications(data.notifications || []);
        setUnreadCount(data.unreadCount || 0);
      }
    } catch (err) {}
  };

  const markNotificationsRead = async (id?: number) => {
    if (!token) return;
    try {
      const res = await fetch(`${API_URL}/notifications/mark-read`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ id }),
      });
      if (res.ok) {
        await fetchNotifications();
      }
    } catch (err) {}
  };

  return (
    <AppContext.Provider
      value={{
        user,
        token,
        tasks,
        attendance,
        notifications,
        unreadCount,
        loading,
        error,
        login,
        logout,
        fetchTasks,
        advanceTaskStep,
        fetchAttendance,
        toggleAttendance,
        submitSurvey,
        fetchNotifications,
        markNotificationsRead,
      }}
    >
      {children}
    </AppContext.Provider>
  );
}

export function useApp() {
  const ctx = useContext(AppContext);
  if (!ctx) throw new Error("useApp must be used within an AppProvider");
  return ctx;
}
