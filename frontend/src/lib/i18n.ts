import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

import Backend from 'i18next-http-backend';

const appLang = localStorage.getItem('appLang') ?? 'es-PE';

i18n
  .use(Backend)
  .use(initReactI18next)
  .init({
    lng: appLang,
    fallbackLng: 'en-US',
    //debug: true,
    backend: {
      loadPath: '/locales/{{lng}}.json',
    },
    interpolation: {
      escapeValue: false,
    },
  });

export const languageOptions = [
  { value: 'es-PE', label: 'Español - Peru' },
  { value: 'en-US', label: 'Ingles - USA' },
];

export default i18n;
