"use client";

import { FormEvent, useState } from "react";
import { useRouter } from "next/navigation";
import { Droplet } from "lucide-react";
import { ContourBackground } from "@/components/ContourBackground";
import { Button } from "@/components/ui/Button";
import { FieldLabel, FieldInput } from "@/components/ui/Field";
import { useApp } from "@/lib/store";

export default function LoginPage() {
  const router = useRouter();
  const { login, error, loading } = useApp();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    const success = await login(email, password);
    if (success) {
      router.push("/home");
    }
  }

  return (
    <div className="flex flex-1 flex-col">
      <div className="relative shrink-0 overflow-hidden bg-gradient-to-br from-aqua-500 to-aqua-800 px-6 pb-12 pt-10 text-white">
        <div className="absolute inset-0">
          <ContourBackground width={400} height={260} bands={4} seed={5} />
        </div>
        <div className="relative z-10">
          <div className="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 backdrop-blur">
            <Droplet className="h-7 w-7" strokeWidth={1.75} />
          </div>
          <h1 className="font-display text-xl font-bold leading-tight tracking-tight">
            Himalayan Water
            <br />
            Science
          </h1>
          <p className="mt-1.5 text-xs font-medium text-aqua-100">Field Service &amp; Sales App</p>
        </div>
        <svg
          viewBox="0 0 400 32"
          preserveAspectRatio="none"
          className="absolute -bottom-px left-0 z-10 h-6 w-full text-slate-50"
          aria-hidden="true"
        >
          <path fill="currentColor" d="M0,16 C100,32 300,0 400,16 L400,32 L0,32 Z" />
        </svg>
      </div>

      <div className="-mt-1 flex-1 px-6 pb-8 pt-2">
        <p className="mb-4 text-sm font-bold text-slate-700">Sign in to continue</p>
        {error && (
          <div className="mb-4 rounded-lg bg-rose-50 p-3 text-xs font-medium text-rose-600">
            {error}
          </div>
        )}
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <FieldLabel htmlFor="email">Mobile number or email</FieldLabel>
            <FieldInput
              id="email"
              type="text"
              placeholder="you@hws.in"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>
          <div>
            <FieldLabel htmlFor="password">Password</FieldLabel>
            <FieldInput
              id="password"
              type="password"
              placeholder="Enter your password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>
          <div className="flex items-center justify-between pt-1 text-xs">
            <label className="flex items-center gap-1.5 font-medium text-slate-500">
              <input type="checkbox" className="rounded accent-aqua-600" />
              Remember me
            </label>
            <a href="#" className="font-semibold text-aqua-600">
              Forgot password?
            </a>
          </div>
          <Button type="submit" block disabled={loading} className="mt-2">
            {loading ? "Signing in..." : "Login"}
          </Button>
          <div className="relative py-2 text-center">
            <span className="relative z-10 bg-slate-50 px-2 text-[11px] text-slate-400">or</span>
            <div className="absolute left-0 top-1/2 h-px w-full bg-slate-200" />
          </div>
          <Button type="button" variant="secondary" block onClick={() => router.push("/home")}>
            Login with OTP
          </Button>
        </form>
        <p className="mt-8 text-center text-[11px] text-slate-400">
          v2.1.0 · Himalayan Water Science Pvt. Ltd.
        </p>
      </div>
    </div>
  );
}
