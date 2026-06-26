<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Annonce extends Model
{
    use HasFactory;

    protected $table = 'annonces';

    public const NIVEAU_INFO = 'info';

    public const NIVEAU_ATTENTION = 'attention';

    public const NIVEAU_URGENCE = 'urgence';

    public const NIVEAU_RAPPEL = 'rappel';

    /** @var list<string> */
    public const NIVEAUX = [
        self::NIVEAU_INFO,
        self::NIVEAU_ATTENTION,
        self::NIVEAU_URGENCE,
        self::NIVEAU_RAPPEL,
    ];

    protected $attributes = [
        'niveau' => self::NIVEAU_INFO,
    ];

    protected $fillable = [
        'titre',
        'niveau',
        'contenu',
        'published_at',
        'expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Annonces visibles pour les utilisateurs (publiées et non expirées).
     */
    public function scopePublique(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function (Builder $q): void {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public function estBrouillon(): bool
    {
        return $this->published_at === null;
    }

    /**
     * Libellés et classes Tailwind par niveau (Filament + portail).
     *
     * @return array{
     *     label: string,
     *     filament: array{card: string, badge: string, title: string, meta: string, body: string},
     *     portal: array{card: string, iconWrap: string, icon: string, title: string, metaStrong: string, metaMuted: string, body: string},
     *     plain: array{card: string, iconWrap: string, icon: string, title: string, meta: string, body: string}
     * }
     */
    public function presentation(): array
    {
        $key = in_array($this->niveau, self::NIVEAUX, true) ? $this->niveau : self::NIVEAU_INFO;

        return match ($key) {
            self::NIVEAU_ATTENTION => [
                'label' => 'Attention',
                'filament' => [
                    'card' => 'rounded-xl border border-amber-200/90 border-l-4 border-l-amber-500 bg-gradient-to-r from-amber-50 to-white p-4 shadow-sm dark:border-amber-400/25 dark:border-l-amber-400 dark:from-amber-500/10 dark:to-white/[0.04]',
                    'badge' => 'mb-2 inline-flex items-center rounded-md bg-amber-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white dark:bg-amber-500/35 dark:text-amber-50',
                    'title' => 'font-semibold text-amber-950 dark:text-amber-50',
                    'meta' => 'mt-1 text-xs text-amber-900/65 dark:text-amber-200/70',
                    'body' => 'mt-2 text-sm leading-relaxed text-amber-950/90 dark:text-amber-50/90',
                ],
                'portal' => [
                    'card' => 'glass rounded-2xl border border-amber-300/45 bg-amber-500/14 p-4 sm:p-5 shadow-sm ring-1 ring-amber-300/25',
                    'iconWrap' => 'flex-shrink-0 rounded-xl bg-amber-500/35 flex items-center justify-center text-amber-50',
                    'icon' => 'text-amber-100',
                    'title' => 'font-bold text-amber-50 tracking-tight',
                    'metaStrong' => 'text-amber-100/95',
                    'metaMuted' => 'text-amber-100/75',
                    'body' => 'text-amber-50/95 whitespace-pre-wrap',
                ],
                'plain' => [
                    'card' => 'rounded-2xl border-2 border-amber-300 bg-amber-50 p-4 sm:p-5 text-slate-900 shadow-sm',
                    'iconWrap' => 'flex-shrink-0 w-10 h-10 rounded-xl bg-amber-200 flex items-center justify-center text-amber-900',
                    'icon' => 'text-amber-900',
                    'title' => 'text-sm font-bold text-amber-950',
                    'meta' => 'text-[11px] text-amber-900/70 mt-0.5',
                    'body' => 'mt-2 text-sm text-slate-800 leading-relaxed whitespace-pre-wrap',
                ],
            ],
            self::NIVEAU_URGENCE => [
                'label' => 'Urgence',
                'filament' => [
                    'card' => 'rounded-xl border border-rose-200/90 border-l-4 border-l-rose-600 bg-gradient-to-r from-rose-50 to-white p-4 shadow-sm dark:border-rose-500/30 dark:border-l-rose-500 dark:from-rose-500/15 dark:to-white/[0.04]',
                    'badge' => 'mb-2 inline-flex items-center rounded-md bg-rose-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white dark:bg-rose-600/40 dark:text-rose-50',
                    'title' => 'font-semibold text-rose-950 dark:text-rose-50',
                    'meta' => 'mt-1 text-xs text-rose-900/65 dark:text-rose-200/70',
                    'body' => 'mt-2 text-sm leading-relaxed text-rose-950/90 dark:text-rose-50/90',
                ],
                'portal' => [
                    'card' => 'glass rounded-2xl border border-rose-400/50 bg-rose-500/15 p-4 sm:p-5 shadow-sm ring-1 ring-rose-400/30',
                    'iconWrap' => 'flex-shrink-0 rounded-xl bg-rose-600/45 flex items-center justify-center text-rose-50',
                    'icon' => 'text-rose-100',
                    'title' => 'font-bold text-rose-50 tracking-tight',
                    'metaStrong' => 'text-rose-100/95',
                    'metaMuted' => 'text-rose-100/75',
                    'body' => 'text-rose-50/95 whitespace-pre-wrap',
                ],
                'plain' => [
                    'card' => 'rounded-2xl border-2 border-rose-400 bg-rose-50 p-4 sm:p-5 text-slate-900 shadow-sm',
                    'iconWrap' => 'flex-shrink-0 w-10 h-10 rounded-xl bg-rose-200 flex items-center justify-center text-rose-900',
                    'icon' => 'text-rose-900',
                    'title' => 'text-sm font-bold text-rose-950',
                    'meta' => 'text-[11px] text-rose-900/75 mt-0.5',
                    'body' => 'mt-2 text-sm text-slate-800 leading-relaxed whitespace-pre-wrap',
                ],
            ],
            self::NIVEAU_RAPPEL => [
                'label' => 'Rappel',
                'filament' => [
                    'card' => 'rounded-xl border border-violet-200/90 border-l-4 border-l-violet-500 bg-gradient-to-r from-violet-50 to-white p-4 shadow-sm dark:border-violet-400/25 dark:border-l-violet-400 dark:from-violet-500/10 dark:to-white/[0.04]',
                    'badge' => 'mb-2 inline-flex items-center rounded-md bg-violet-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white dark:bg-violet-500/35 dark:text-violet-100',
                    'title' => 'font-semibold text-violet-950 dark:text-violet-50',
                    'meta' => 'mt-1 text-xs text-violet-900/65 dark:text-violet-200/70',
                    'body' => 'mt-2 text-sm leading-relaxed text-violet-950/90 dark:text-violet-50/90',
                ],
                'portal' => [
                    'card' => 'glass rounded-2xl border border-violet-300/45 bg-violet-500/14 p-4 sm:p-5 shadow-sm ring-1 ring-violet-300/25',
                    'iconWrap' => 'flex-shrink-0 rounded-xl bg-violet-500/35 flex items-center justify-center text-violet-50',
                    'icon' => 'text-violet-100',
                    'title' => 'font-bold text-violet-50 tracking-tight',
                    'metaStrong' => 'text-violet-100/95',
                    'metaMuted' => 'text-violet-100/75',
                    'body' => 'text-violet-50/95 whitespace-pre-wrap',
                ],
                'plain' => [
                    'card' => 'rounded-2xl border-2 border-violet-300 bg-violet-50 p-4 sm:p-5 text-slate-900 shadow-sm',
                    'iconWrap' => 'flex-shrink-0 w-10 h-10 rounded-xl bg-violet-200 flex items-center justify-center text-violet-900',
                    'icon' => 'text-violet-900',
                    'title' => 'text-sm font-bold text-violet-950',
                    'meta' => 'text-[11px] text-violet-900/70 mt-0.5',
                    'body' => 'mt-2 text-sm text-slate-800 leading-relaxed whitespace-pre-wrap',
                ],
            ],
            default => [
                'label' => 'Information',
                'filament' => [
                    'card' => 'rounded-xl border border-sky-200/90 border-l-4 border-l-sky-500 bg-gradient-to-r from-sky-50 to-white p-4 shadow-sm dark:border-sky-500/25 dark:border-l-sky-400 dark:from-sky-500/10 dark:to-white/[0.04]',
                    'badge' => 'mb-2 inline-flex items-center rounded-md bg-sky-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white dark:bg-sky-500/35 dark:text-sky-100',
                    'title' => 'font-semibold text-sky-950 dark:text-sky-50',
                    'meta' => 'mt-1 text-xs text-sky-900/65 dark:text-sky-200/70',
                    'body' => 'mt-2 text-sm leading-relaxed text-sky-950/90 dark:text-sky-50/90',
                ],
                'portal' => [
                    'card' => 'glass rounded-2xl border border-sky-300/45 bg-sky-500/14 p-4 sm:p-5 shadow-sm ring-1 ring-sky-300/25',
                    'iconWrap' => 'flex-shrink-0 rounded-xl bg-sky-500/35 flex items-center justify-center text-sky-50',
                    'icon' => 'text-sky-100',
                    'title' => 'font-bold text-sky-50 tracking-tight',
                    'metaStrong' => 'text-sky-100/95',
                    'metaMuted' => 'text-sky-100/75',
                    'body' => 'text-sky-50/95 whitespace-pre-wrap',
                ],
                'plain' => [
                    'card' => 'rounded-2xl border-2 border-sky-300 bg-sky-50 p-4 sm:p-5 text-slate-900 shadow-sm',
                    'iconWrap' => 'flex-shrink-0 w-10 h-10 rounded-xl bg-sky-200 flex items-center justify-center text-sky-900',
                    'icon' => 'text-sky-900',
                    'title' => 'text-sm font-bold text-sky-950',
                    'meta' => 'text-[11px] text-sky-900/70 mt-0.5',
                    'body' => 'mt-2 text-sm text-slate-800 leading-relaxed whitespace-pre-wrap',
                ],
            ],
        };
    }

    public static function niveauOptions(): array
    {
        return [
            self::NIVEAU_INFO => 'Information (général)',
            self::NIVEAU_ATTENTION => 'Attention / consigne',
            self::NIVEAU_URGENCE => 'Urgence',
            self::NIVEAU_RAPPEL => 'Rappel / échéance',
        ];
    }
}
