interface ContourBackgroundProps {
  width: number;
  height: number;
  bands?: number;
  seed?: number;
  className?: string;
}

/**
 * Generative topographic-line motif — a nod to the "Himalayan" in the brand.
 * Pure function of its inputs, so it renders identically on server and client.
 */
export function ContourBackground({
  width,
  height,
  bands = 4,
  seed = 1,
  className,
}: ContourBackgroundProps) {
  const paths: { d: string; opacity: number }[] = [];

  for (let i = 0; i < bands; i++) {
    const baseY = height * (0.15 + i * (0.7 / bands)) * (0.8 + 0.2 * Math.sin(seed + i));
    const amp = 18 + i * 6;
    const steps = 6;
    let d = `M 0 ${baseY.toFixed(1)}`;
    for (let s = 1; s <= steps; s++) {
      const x = (width / steps) * s;
      const cx = x - width / steps / 2;
      const cy = baseY + amp * Math.sin(s * 1.3 + seed + i);
      d += ` Q ${cx.toFixed(1)} ${cy.toFixed(1)} ${x.toFixed(1)} ${baseY.toFixed(1)}`;
    }
    d += ` L ${width} ${height} L 0 ${height} Z`;
    paths.push({ d, opacity: 0.14 - i * 0.028 });
  }

  return (
    <svg
      viewBox={`0 0 ${width} ${height}`}
      preserveAspectRatio="none"
      className={className ?? "h-full w-full"}
      aria-hidden="true"
    >
      {paths.map((p, i) => (
        <path key={i} d={p.d} fill="white" opacity={p.opacity} />
      ))}
    </svg>
  );
}
