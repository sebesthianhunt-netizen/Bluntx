import i18n from 'i18next'
import { initReactI18next } from 'react-i18next'
import en from '@/locales/en.json'
import pidgin from '@/locales/pidgin.json'

void i18n
  .use(initReactI18next)
  .init({
    resources: {
      en: { translation: en as Record<string, unknown> },
      pg: { translation: pidgin as Record<string, unknown> },
    },
    lng: 'en',
    fallbackLng: 'en',
    interpolation: { escapeValue: false },
  })

export function setLanguage(lang: 'en' | 'pg') {
  void i18n.changeLanguage(lang)
}

export default i18n
