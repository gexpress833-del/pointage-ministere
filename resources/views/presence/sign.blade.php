<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <title>Signature de présence</title>
    <x-vite-tailwind />
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }

        body { background: #f1f5f9; min-height: 100dvh; overflow-x: hidden; }

        /* Empêche le scroll quand la caméra est active (iOS) */
        body.camera-active { overflow: hidden; position: fixed; width: 100%; left: 0; right: 0; }

        /* Badge de statut animé */
        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,.4); }
            50%       { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
        }
        .pulse-green { animation: pulse-green 1.5s infinite; }

        /* Barre de progression reconnaissance */
        @keyframes scan-line {
            0%   { top: 0%; opacity: 1; }
            90%  { top: 100%; opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .scan-line {
            position: absolute;
            left: 0; right: 0; height: 2px;
            background: linear-gradient(to right, transparent, #3b82f6, transparent);
            animation: scan-line 2s ease-in-out infinite;
        }

        /* Video miroir + canvas overlay aligné */
        #video, #canvas { transform: scaleX(-1); }

        /* Bouton plein écran sur mobile */
        @media (max-width: 480px) {
            .btn-primary {
                font-size: 1.1rem;
                padding: 1rem 1.5rem;
            }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen min-h-dvh overflow-x-hidden antialiased">

    {{-- En-tête --}}
    <header class="bg-blue-700 text-white px-4 py-3 flex items-center gap-3 shadow-md flex-shrink-0" style="padding-top: max(0.75rem, env(safe-area-inset-top, 0px));">
        <a href="{{ route('presence.dashboard') }}" class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] text-white opacity-75 hover:opacity-100 transition -ml-1 rounded-full" title="Tableau de bord">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-semibold leading-tight truncate">Signature de présence</h1>
            <p class="text-xs text-blue-200 leading-tight">{{ config('app.name') }}</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('presence.historique') }}"
               class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] text-white opacity-75 hover:opacity-100 transition rounded-full"
               title="Historique">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                </svg>
            </a>
            <div class="text-xs text-blue-200 text-right">
                <div id="clock" class="font-mono text-sm font-semibold"></div>
                <div>{{ now()->translatedFormat('d M Y') }}</div>
            </div>
        </div>
    </header>

    {{-- Contenu principal --}}
    <main class="flex-1 flex flex-col items-center justify-start p-4 gap-4 max-w-lg mx-auto w-full min-w-0" style="padding-bottom: max(1rem, env(safe-area-inset-bottom, 0px));">

        @include('presence.partials.annonces', ['annoncesVariant' => 'plain'])

        {{-- Carte infos utilisateur --}}
        <div class="w-full bg-white rounded-2xl shadow p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 overflow-hidden border border-blue-100">
                @if(!empty($agentPhotoUrl))
                    <img
                        src="{{ $agentPhotoUrl }}"
                        alt="Photo de {{ auth()->user()->getDisplayName() }}"
                        class="w-full h-full object-cover"
                    >
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-gray-800 truncate">{{ auth()->user()->getDisplayName() }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->matricule ?? auth()->user()->email }}</p>
            </div>
        </div>

        {{-- Étape 1 : bouton lancer le scan --}}
        <div id="stepStart" class="w-full">
            <div class="bg-white rounded-2xl shadow p-5 text-center space-y-4">
                <div class="w-16 h-16 mx-auto bg-blue-50 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5 20.47 5m0 0H15m5.47 0v5M4.5 19.5l-.53-.53m0 0-3.53-3.53m3.53 3.53 3.53-3.53m-7.06-7.06 3.53 3.53m0 0L8 16m-4-4.47L8.5 8.03" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        @if(($signMode ?? 'arrival') === 'departure')
                            Pointer mon départ
                        @else
                            Signer ma présence
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        @if(($signMode ?? 'arrival') === 'departure')
                            Enregistrez votre heure de sortie avec la même reconnaissance faciale que à l'arrivée.
                        @else
                            La caméra s'ouvrira pour reconnaître votre visage.<br>L'arrivée sera enregistrée automatiquement.
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-2">Si la caméra ne démarre pas, autorisez l'accès caméra dans le navigateur et fermez les autres applications qui l'utilisent.</p>
                </div>
                <button type="button" id="btnSigner"
                    class="btn-primary w-full py-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold rounded-xl shadow-md transition-all duration-150 focus:outline-none focus:ring-4 focus:ring-blue-300 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                    Scanner mon visage
                </button>
            </div>
        </div>

        {{-- Étape 2 : vue caméra --}}
        <div id="stepScan" class="hidden w-full space-y-3">
            {{-- Zone vidéo --}}
            <div class="relative rounded-2xl overflow-hidden bg-gray-900 shadow-lg" style="aspect-ratio:4/3;">
                <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                <canvas id="canvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
                <div class="scan-line" id="scanLine"></div>
                {{-- Overlay cadre visage --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="border-2 border-white/40 rounded-full" style="width:45%;aspect-ratio:1/1;"></div>
                </div>
                {{-- Bandeau statut --}}
                <div id="statusBar" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-3 pb-3 pt-6">
                    <div id="status" class="text-white text-sm font-medium text-center">Préparation...</div>
                </div>
            </div>

            {{-- Barre de chargement --}}
            <div id="progressWrap" class="w-full">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span id="progressLabel">Chargement des modèles...</span>
                    <span id="progressPct">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width:0%"></div>
                </div>
            </div>

            <button type="button" id="btnAnnuler"
                class="w-full py-3 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-medium rounded-xl transition-all duration-150">
                Annuler
            </button>
        </div>

        {{-- Succès --}}
        <div id="stepSuccess" class="hidden w-full">
            <div class="bg-white rounded-2xl shadow p-6 text-center space-y-4">
                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center pulse-green">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-green-700" id="successTitle">{{ ($signMode ?? 'arrival') === 'departure' ? 'Départ enregistré' : 'Présence enregistrée' }}</h2>
                    <p class="text-sm text-gray-500 mt-1" id="heureEnregistree"></p>
                </div>
                {{-- Aperçu de la photo capturée --}}
                <div class="flex justify-center">
                    <img id="capturePreview"
                        class="hidden w-28 h-28 object-cover rounded-full border-4 border-green-300 shadow"
                        alt="Photo de signature">
                </div>
                <p class="text-xs text-gray-400">Photo enregistrée comme preuve de présence</p>
                <a href="{{ route('presence.historique') }}"
                    class="block w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition">
                    Voir mon historique
                </a>
            </div>
        </div>

        {{-- Erreur --}}
        <div id="stepError" class="hidden w-full">
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-center space-y-3">
                <div class="w-12 h-12 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <p id="errorText" class="text-red-700 font-medium text-sm"></p>
                <button type="button" id="btnReessayer"
                    class="w-full py-3 bg-white border border-red-300 hover:bg-red-50 text-red-700 font-medium rounded-xl transition">
                    Réessayer
                </button>
                <a href="{{ route('presence.dashboard') }}" class="block text-sm text-gray-500 hover:underline">
                    Retour au tableau de bord
                </a>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@2.14.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
    // Attendre que face-api soit chargé
    function waitForFaceApi(callback, maxAttempts = 50) {
        let attempts = 0;
        const checkInterval = setInterval(() => {
            attempts++;
            if (typeof faceapi !== 'undefined') {
                clearInterval(checkInterval);
                callback();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                console.error('face-api.js failed to load after ' + maxAttempts + ' attempts');
                alert('Erreur de chargement de la bibliothèque de reconnaissance faciale. Veuillez rafraîchir la page.');
            }
        }, 100);
    }

    waitForFaceApi(function() {
    (function () {
        // ── Références DOM ──────────────────────────────────────────────────
        const stepStart   = document.getElementById('stepStart');
        const stepScan    = document.getElementById('stepScan');
        const stepSuccess = document.getElementById('stepSuccess');
        const stepError   = document.getElementById('stepError');
        const btnSigner   = document.getElementById('btnSigner');
        const btnAnnuler  = document.getElementById('btnAnnuler');
        const btnReessayer = document.getElementById('btnReessayer');
        const video       = document.getElementById('video');
        const canvas      = document.getElementById('canvas');
        const statusEl    = document.getElementById('status');
        const progressBar = document.getElementById('progressBar');
        const progressPct = document.getElementById('progressPct');
        const progressLabel = document.getElementById('progressLabel');
        const progressWrap  = document.getElementById('progressWrap');
        const heureEl     = document.getElementById('heureEnregistree');
        const errorText   = document.getElementById('errorText');
        const scanLine    = document.getElementById('scanLine');
        const clock       = document.getElementById('clock');

        // ── Config depuis PHP ────────────────────────────────────────────────
        const MODEL_URL          = '{{ asset("models") }}';
        const referencePhotoUrl  = @json($referencePhotoUrl);
        const sessionId          = {{ $sessionId }};
        const submitSignUrl      = @json($submitSignUrl ?? route('presence.sign.submit'));
        const signMode           = @json($signMode ?? 'arrival');
        /** Distance euclidienne max (déjà normalisée côté serveur, typ. 0,45–0,60) */
        const threshold          = {{ $seuilReconnaissance ?? 0.55 }};
        const referenceCacheKey  = @json('presence-ref-' . auth()->id() . '-' . md5((string) auth()->user()->photo_reference));

        // ── Horloge ──────────────────────────────────────────────────────────
        function updateClock() {
            const now = new Date();
            clock.textContent = now.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit', second: '2-digit'});
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ── État ─────────────────────────────────────────────────────────────
        let stream = null;
        let scanInterval = null;
        let signed = false;
        let checking = false;
        let modelsLoaded = false;
        let refDescriptor = null;
        let modelsPromise = null;
        let referencePromise = null;

        // ── Utilitaires affichage ────────────────────────────────────────────
        function setStep(name) {
            [stepStart, stepScan, stepSuccess, stepError].forEach(el => el.classList.add('hidden'));
            document.getElementById('step' + name)?.classList.remove('hidden');
        }

        function setStatus(msg) {
            statusEl.textContent = msg;
        }

        function setProgress(pct, label) {
            progressBar.style.width = pct + '%';
            progressPct.textContent = pct + '%';
            if (label) progressLabel.textContent = label;
        }

        function showError(msg) {
            errorText.textContent = msg;
            setCameraActive(false);
            stopCamera();
            setStep('Error');
        }

        function setCameraActive(active) {
            document.body.classList.toggle('camera-active', active);
        }

        function getCameraErrorMessage(error) {
            const rawMessage = error?.message || '';
            const name = error?.name || '';

            if (!navigator.mediaDevices?.getUserMedia) {
                return 'Votre navigateur ne prend pas en charge l\'accès à la caméra.';
            }

            if (rawMessage === 'CameraUnsupported') {
                return 'Votre navigateur ne prend pas en charge l\'accès à la caméra.';
            }

            if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
                return 'Accès à la caméra refusé. Autorisez la caméra dans votre navigateur puis réessayez.';
            }

            if (name === 'NotFoundError' || name === 'DevicesNotFoundError') {
                return 'Aucune caméra détectée sur cet appareil.';
            }

            if (name === 'NotReadableError' || name === 'TrackStartError') {
                return 'La caméra est déjà utilisée par une autre application. Fermez Teams, Zoom, WhatsApp ou la caméra Windows puis réessayez.';
            }

            if (name === 'AbortError' || rawMessage.includes('Timeout starting video source')) {
                return 'La caméra a mis trop de temps à démarrer. Vérifiez l\'autorisation caméra et fermez les autres applications qui l\'utilisent.';
            }

            if (name === 'OverconstrainedError') {
                return 'Les paramètres vidéo demandés ne sont pas supportés par votre caméra.';
            }

            return 'Impossible d\'accéder à la caméra. Vérifiez les permissions de votre navigateur.';
        }

        // ── Caméra ────────────────────────────────────────────────────────────
        function stopCamera() {
            if (scanInterval) { clearInterval(scanInterval); scanInterval = null; }
            if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
            if (scanLine) scanLine.style.display = 'none';
        }

        async function startCamera() {
            if (!navigator.mediaDevices?.getUserMedia) {
                throw new Error('CameraUnsupported');
            }

            const constraints = {
                video: {
                    facingMode: 'user',
                    width: { ideal: 480 },
                    height: { ideal: 360 },
                    frameRate: { ideal: 24, max: 30 }
                }
            };

            try {
                stream = await navigator.mediaDevices.getUserMedia(constraints);
            } catch (error) {
                if (error?.name === 'OverconstrainedError') {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                } else {
                    throw error;
                }
            }

            video.srcObject = stream;

            await new Promise((resolve, reject) => {
                const timeoutId = setTimeout(() => {
                    reject(new Error('Timeout starting video source'));
                }, 12000);

                video.onloadedmetadata = async () => {
                    try {
                        await video.play();
                        clearTimeout(timeoutId);
                        resolve();
                    } catch (error) {
                        clearTimeout(timeoutId);
                        reject(error);
                    }
                };
            });
        }

        // ── Chargement des modèles ─────────────────────────────────────────
        async function loadModels() {
            if (modelsLoaded) return;
            if (!modelsPromise) {
                modelsPromise = (async () => {
                    setProgress(10, 'Chargement du détecteur...');
                    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                    setProgress(40, 'Chargement des landmarks...');
                    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                    setProgress(70, 'Chargement de la reconnaissance...');
                    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                    setProgress(100, 'Modèles chargés');
                    modelsLoaded = true;
                })().catch((error) => {
                    modelsPromise = null;
                    throw error;
                });
            }

            await modelsPromise;
        }

        // ── Photo de référence ────────────────────────────────────────────
        async function loadReferenceDescriptor() {
            if (refDescriptor) return refDescriptor;

            try {
                const cached = sessionStorage.getItem(referenceCacheKey);
                if (cached) {
                    refDescriptor = new Float32Array(JSON.parse(cached));
                    return refDescriptor;
                }
            } catch (e) {
                console.warn('Cache photo de référence ignoré :', e);
            }

            await loadModels();

            if (!referencePromise) {
                referencePromise = new Promise((resolve, reject) => {
                    if (!referencePhotoUrl) {
                        reject(new Error('Photo de référence non disponible. Contactez l\'administrateur.'));
                        return;
                    }

                    const img = new Image();
                    img.crossOrigin = 'anonymous';
                    img.onload = async () => {
                        try {
                            const det = await faceapi
                                .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.4 }))
                                .withFaceLandmarks()
                                .withFaceDescriptor();

                            if (!det) {
                                reject(new Error('Aucun visage détecté dans votre photo de référence. Contactez l\'administrateur.'));
                                return;
                            }

                            refDescriptor = det.descriptor;
                            try {
                                sessionStorage.setItem(referenceCacheKey, JSON.stringify(Array.from(det.descriptor)));
                            } catch (e) {
                                console.warn('Impossible de mettre en cache la photo de référence :', e);
                            }
                            resolve(refDescriptor);
                        } catch (e) {
                            reject(e);
                        }
                    };
                    img.onerror = () => reject(new Error('Impossible de charger la photo de référence.'));
                    img.src = referencePhotoUrl + (referencePhotoUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
                }).catch((error) => {
                    referencePromise = null;
                    throw error;
                });
            }

            return referencePromise;
        }

        function preloadRecognitionAssets() {
            if (!referencePhotoUrl) return;

            loadModels().catch(() => {});

            Promise.resolve(modelsPromise)
                .then(() => loadReferenceDescriptor())
                .catch(() => {});
        }

        // ── Détection & comparaison ───────────────────────────────────────
        async function detectAndCompare() {
            if (signed || checking || !video.videoWidth) return;
            checking = true;

            try {
                if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                }

                const dims = { width: video.videoWidth, height: video.videoHeight };
                const detection = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.4 }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

                if (!detection) {
                    setStatus('Aucun visage détecté – Positionnez-vous face à la caméra');
                    checking = false;
                    return;
                }

                const bestDist = faceapi.euclideanDistance(refDescriptor, detection.descriptor);
                console.log('face sign debug', { bestDist, threshold });

                // Match si distance <= seuil max (plus petit = visages plus proches)
                if (bestDist <= threshold) {
                    signed = true;
                    clearInterval(scanInterval);
                    scanInterval = null;
                    scanLine.style.display = 'none';
                    setStatus('Visage reconnu ✓ – Enregistrement en cours...');
                    await submitPresence();
                } else {
                    setStatus(
                        `Visage détecté – distance ${bestDist.toFixed(2)} (max ${threshold.toFixed(2)}) – rapprochez-vous ou améliorez la lumière`
                    );
                }
            } catch (e) {
                console.error(e);
            }

            checking = false;
        }

        // ── Capture photo depuis la webcam ───────────────────────────────
        function capturePhoto() {
            try {
                const cap = document.createElement('canvas');
                cap.width  = video.videoWidth  || 640;
                cap.height = video.videoHeight || 480;
                // La vidéo est mirrée via CSS (scaleX(-1)), on fait pareil sur le canvas
                const ctx = cap.getContext('2d');
                ctx.translate(cap.width, 0);
                ctx.scale(-1, 1);
                ctx.drawImage(video, 0, 0, cap.width, cap.height);
                return cap.toDataURL('image/jpeg', 0.75); // qualité 75%
            } catch (e) {
                console.warn('Capture photo échouée :', e);
                return null;
            }
        }

        // ── Envoi présence ────────────────────────────────────────────────
        async function submitPresence() {
            try {
                // Capture le visage avant d'arrêter la caméra
                const photoDataUrl = capturePhoto();

                const formData = new FormData();
                formData.append('session_id', sessionId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                if (photoDataUrl) {
                    formData.append('photo_capture', photoDataUrl);
                }

                const resp = await fetch(submitSignUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                let data = null;
                try {
                    data = await resp.json();
                } catch (e) {
                    console.error('Invalid JSON from sign endpoint', await resp.text());
                }
                console.log('submitPresence response', resp.status, data);

                stopCamera();
                setCameraActive(false);

                if (resp.status === 419) {
                    showError('Session expirée. Rechargez la page puis réessayez.');
                    return;
                }

                if (data && data.success) {
                    // Afficher la photo capturée dans l'écran de succès
                    if (photoDataUrl) {
                        const imgEl = document.getElementById('capturePreview');
                        if (imgEl) {
                            imgEl.src = photoDataUrl;
                            imgEl.classList.remove('hidden');
                        }
                    }
                    const titleEl = document.getElementById('successTitle');
                    if (titleEl) {
                        titleEl.textContent = (data.kind === 'departure' || signMode === 'departure')
                            ? 'Départ enregistré'
                            : 'Présence enregistrée';
                    }
                    heureEl.textContent = (data.kind === 'departure' || signMode === 'departure')
                        ? ('Heure de départ : ' + (data.heure || ''))
                        : ('Heure d\'arrivée : ' + (data.heure || ''));
                    setStep('Success');
                } else if (data && data.message) {
                    showError(data.message);
                } else {
                    showError(resp.ok
                        ? 'Une erreur est survenue.'
                        : 'Erreur serveur (' + resp.status + '). Réessayez ou rechargez la page.');
                }
            } catch (e) {
                setCameraActive(false);
                showError('Erreur réseau : ' + e.message);
            }
        }

        // ── Flux principal ────────────────────────────────────────────────
        async function runFaceSign() {
            setStep('Scan');
            setCameraActive(true);
            progressWrap.classList.remove('hidden');
            setProgress(0, 'Initialisation...');
            setStatus('Préparation du scan...');
            scanLine.style.display = 'none';

            try {
                await Promise.all([
                    loadModels(),
                    startCamera(),
                ]);
            } catch (e) {
                console.error('Init scan error:', e);
                showError(getCameraErrorMessage(e));
                return;
            }

            setStatus(refDescriptor ? 'Photo de référence prête...' : 'Préparation de votre photo de référence...');
            try {
                refDescriptor = await loadReferenceDescriptor();
            } catch (e) {
                console.error('Reference photo error:', e);
                showError(e.message || 'Impossible de préparer la photo de référence.');
                return;
            }
            progressWrap.classList.add('hidden');
            scanLine.style.display = '';
            setStatus('Positionnez votre visage dans le cercle...');
            scanInterval = setInterval(detectAndCompare, 450);
        }

        // ── Événements ────────────────────────────────────────────────────
        btnSigner.addEventListener('click', () => {
            if (!referencePhotoUrl) {
                showError('Photo de référence non configurée. Contactez l\'administrateur.');
                return;
            }
            runFaceSign();
        });

        btnAnnuler.addEventListener('click', () => {
            signed = false;
            setCameraActive(false);
            stopCamera();
            setStep('Start');
        });

        btnReessayer.addEventListener('click', () => {
            signed = false;
            runFaceSign();
        });

        // Précharge les modèles et la photo de référence en arrière-plan
        setTimeout(() => {
            preloadRecognitionAssets();
        }, 300);

    })();
    });
    </script>
</body>
</html>
