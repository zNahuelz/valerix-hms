import { createRoot } from 'react-dom/client';
import { RouterProvider } from 'react-router';
import { router } from './router/index.ts';
import './index.css';
import { TooltipProvider } from './components/ui/tooltip.tsx';
import { ThemeProvider } from './components/ThemeProvider.tsx';
import i18n from './lib/i18n.ts';

createRoot(document.getElementById('root')!).render(
  <ThemeProvider defaultTheme='light' storageKey='app-theme'>
    <TooltipProvider delayDuration={0}>
      <RouterProvider router={router}></RouterProvider>
    </TooltipProvider>
  </ThemeProvider>
);
