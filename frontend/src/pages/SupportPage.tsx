import { useTranslation } from 'react-i18next'

export default function SupportPage() {
  const { t } = useTranslation()
  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">{t('support.help_headline')}</h1>
      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-2">
        <a className="text-primary" href="#">{t('support.faq')}</a>
        <a className="text-primary" href="#">{t('support.contact_support')}</a>
        <a className="text-primary" href="#">{t('support.report_issue')}</a>
        <a className="text-primary" href="#">{t('support.chat_with_us')}</a>
      </div>
    </section>
  )
}
