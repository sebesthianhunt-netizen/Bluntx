import { useTranslation } from 'react-i18next'
import { useFeed } from '@/lib/hooks'
import { useState } from 'react'
import { apiFetch } from '@/lib/apiClient'
import { useSessionStore } from '@/store/session'
import { toast } from '@/store/toast'

export default function FeedPage() {
  const { t } = useTranslation()
  const token = useSessionStore((s) => s.accessToken)
  const { data, isLoading } = useFeed()
  const [caption, setCaption] = useState('')

  async function createPost() {
    try {
      await apiFetch('/feed', { method: 'POST', token, body: { type: 'text', caption } })
      toast.success('Posted')
      setCaption('')
    } catch (e: any) {
      toast.error(e?.data?.message || 'Failed to post')
    }
  }

  return (
    <section className="space-y-3">
      <h1 className="text-2xl font-bold">{t('feed.clips_feed')}</h1>

      <div className="bg-card border border-white/5 rounded-xl p-6 space-y-2">
        <div className="font-semibold">Create Post</div>
        <div className="flex gap-2">
          <input className="flex-1 bg-background border border-white/10 rounded px-3 py-2" placeholder="Say something…" value={caption} onChange={(e) => setCaption(e.target.value)} />
          <button onClick={() => void createPost()} className="bg-primary text-black px-4 py-2 rounded-md">Post</button>
        </div>
      </div>

      <div className="bg-card border border-white/5 rounded-xl p-6">
        {isLoading && <div className="text-text-muted">Loading…</div>}
        <div className="space-y-3">
          {data?.data?.length ? (
            data.data.map((p) => (
              <div key={p.id} className="border border-white/10 rounded-lg p-4">
                <div className="text-sm text-text-muted mb-1">by #{p.user_id}</div>
                <div className="font-semibold mb-1">{p.type.toUpperCase()}</div>
                {p.caption && <div>{p.caption}</div>}
              </div>
            ))
          ) : (
            <div className="text-text-muted">{t('feed.no_clips')}</div>
          )}
        </div>
      </div>
    </section>
  )
}
