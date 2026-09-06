import { MetadataRoute } from 'next';

export const dynamic = 'force-static';

export default function manifest(): MetadataRoute.Manifest {
  return {
    name: 'Himalayan Water Science — Employee App',
    short_name: 'HWS Employee',
    description: 'Field service & sales app for Himalayan Water Science',
    start_url: '/home/',
    display: 'standalone',
    background_color: '#ffffff',
    theme_color: '#1d7c88',
    icons: [
      {
        src: '/icon.png',
        sizes: '192x192',
        type: 'image/png',
      },
    ],
  };
}
