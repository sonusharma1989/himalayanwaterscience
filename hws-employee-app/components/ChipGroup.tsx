import { Pill } from "@/components/ui/Pill";

interface ChipGroupProps {
  options: string[];
  multi?: boolean;
  value: string[];
  onChange: (value: string[]) => void;
}

export function ChipGroup({ options, multi = false, value, onChange }: ChipGroupProps) {
  function toggle(option: string) {
    if (multi) {
      onChange(
        value.includes(option) ? value.filter((v) => v !== option) : [...value, option]
      );
    } else {
      onChange([option]);
    }
  }

  return (
    <div className="flex flex-wrap gap-2">
      {options.map((opt) => (
        <Pill key={opt} active={value.includes(opt)} onClick={() => toggle(opt)}>
          {opt}
        </Pill>
      ))}
    </div>
  );
}
