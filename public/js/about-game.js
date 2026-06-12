/* public/js/about-game.js */
document.addEventListener('DOMContentLoaded', () => {
    // Game Data
    const teamData = [
        {
            id: "daniswat",
            name: "Daniswat",
            role: "Kepala Bagian Aset & GA",
            avatar: "img/dani.jpeg",
            position: { top: "76%", left: "51.1%" }, // Chair 3 (Center/Boss area)
            dialog: [
                "Halo, selamat datang di kantor pusat kami.",
                "Saya memastikan seluruh operasional berjalan lancar dan terstruktur.",
                "Silakan berkeliling dan sapa anggota tim yang lain!"
            ]
        },
        {
            id: "ryan",
            name: "Ryan Putra Pratama",
            role: "Kepala Unit GA",
            avatar: "img/ryan.jpeg",
            position: { top: "76%", left: "29.3%" }, // Chair 2
            dialog: [
                "Yo! Saya mengurus segala urusan General Affair.",
                "Kenyamanan ruangan dan ketersediaan snack adalah tanggung jawab saya.",
                "Mau snack? Haha, canda."
            ]
        },
        {
            id: "yogi",
            name: "Yogi Ramadhandi",
            role: "Staf Asset",
            avatar: "img/yogi.jpeg",
            position: { top: "76%", left: "71.9%" }, // Chair 4
            dialog: [
                "Hai. Saya bertugas di bagian logistik dan penerimaan barang.",
                "Semua barang yang masuk dan keluar saya catat dengan rapi."
            ]
        },
        {
            id: "dodo",
            name: "Muhammad Ikhwanul Widodo",
            role: "Staf GA",
            avatar: "img/dodo.jpeg",
            position: { top: "76%", left: "90.6%" }, // Chair 5
            dialog: [
                "Halo! Kebersihan dan teknis lapangan adalah makanan sehari-hari saya.",
                "Kalau ada fasilitas yang rusak, lapor ke saya ya."
            ]
        },
        {
            id: "habi",
            name: "Habi Islami",
            role: "IT Support",
            avatar: "img/habiislami.jpeg",
            position: { top: "76%", left: "9.6%" }, // Chair 1
            dialog: [
                "Bip bop. Saya IT Support di sini.",
                "Menulis kode, memperbaiki bug, dan memastikan sistem berjalan stabil.",
                "Dan ya, saya yang bikin halaman ini. Keren kan?"
            ]
        }
    ];

    // State Management
    let visitedCharacters = JSON.parse(localStorage.getItem('visitedCharacters_about')) || [];
    // Filter out any characters that no longer exist in teamData
    const validIds = teamData.map(char => char.id);
    visitedCharacters = visitedCharacters.filter(id => validIds.includes(id));

    let currentCharacter = null;
    let currentDialogIndex = 0;
    let isTyping = false;
    let typeInterval;
    let introFinished = localStorage.getItem('introFinished_about') === 'true';

    // DOM Elements
    const officeEnvironment = document.getElementById('office-environment');
    const introOverlay = document.getElementById('intro-overlay');
    const introText = document.getElementById('intro-text');
    const btnNextIntro = document.getElementById('btn-next-intro');
    const btnSkipIntro = document.getElementById('btn-skip-intro');

    const dialogBox = document.getElementById('dialog-box');
    const dialogName = document.getElementById('dialog-name');
    const dialogRole = document.getElementById('dialog-role');
    const dialogText = document.getElementById('dialog-text');
    const dialogAvatar = document.getElementById('dialog-avatar');
    const btnNextDialog = document.getElementById('btn-next-dialog');
    const btnCloseDialog = document.getElementById('btn-close-dialog');

    const progressFill = document.getElementById('progress-bar-fill');
    const progressCount = document.getElementById('progress-count');
    const achievementPopup = document.getElementById('achievement-popup');

    // Initialize Game
    initGame();

    function initGame() {
        renderCharacters();
        updateProgressUI();

        if (!introFinished) {
            playIntro();
        } else {
            introOverlay.classList.add('hidden');
        }
    }

    // Render Characters to Office
    function renderCharacters() {
        // Clear existing characters to prevent duplicates on reset
        document.querySelectorAll('.interactive-character').forEach(el => el.remove());

        teamData.forEach(char => {
            const charDiv = document.createElement('div');
            charDiv.className = `interactive-character ${visitedCharacters.includes(char.id) ? 'visited' : ''}`;
            charDiv.style.setProperty('--base-top', char.position.top);
            charDiv.style.top = char.position.top;
            charDiv.style.left = char.position.left;
            charDiv.title = char.name;

            const img = document.createElement('img');
            img.src = char.avatar;
            img.alt = char.name;

            charDiv.appendChild(img);

            charDiv.addEventListener('click', () => openDialog(char, charDiv));

            officeEnvironment.appendChild(charDiv);
        });
    }

    // Typewriter Effect
    function typeWriter(text, element, speed = 30, callback = null) {
        element.innerHTML = '';
        let i = 0;
        isTyping = true;
        clearInterval(typeInterval);

        typeInterval = setInterval(() => {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
            } else {
                clearInterval(typeInterval);
                isTyping = false;
                if (callback) callback();
            }
        }, speed);
    }

    // Stop Typewriter instantly
    function stopTypeWriter(fullText, element) {
        clearInterval(typeInterval);
        element.innerHTML = fullText;
        isTyping = false;
    }

    // Intro Sequence
    const introDialogs = [
        "Halo! Selamat datang di kantor BMI Pusat. Saya Habi Islami dari IT Support, pemandu sekaligus developer halaman ini.",
        "Di sini, Anda bisa mengenal seluruh tim Bagian Aset & GA dengan cara yang interaktif.",
        "Silakan klik setiap anggota tim yang ada di ruangan ini untuk berkenalan dengan mereka. Selamat menjelajah!"
    ];
    let introIdx = 0;

    function playIntro() {
        introIdx = 0;
        btnNextIntro.textContent = 'Next';
        showIntroText();
    }

    function showIntroText() {
        typeWriter(introDialogs[introIdx], introText);
    }

    btnNextIntro.addEventListener('click', () => {
        if (!introOverlay.classList.contains('hidden')) {
            if (isTyping) {
                stopTypeWriter(introDialogs[introIdx], introText);
            } else {
                introIdx++;
                if (introIdx < introDialogs.length) {
                    showIntroText();
                    if (introIdx === introDialogs.length - 1) {
                        btnNextIntro.textContent = 'Mulai';
                    }
                } else {
                    finishIntro();
                }
            }
        }
    });

    btnSkipIntro.addEventListener('click', () => {
        if (!introOverlay.classList.contains('hidden')) {
            finishIntro();
        }
    });

    function finishIntro() {
        introOverlay.classList.add('hidden');
        localStorage.setItem('introFinished_about', 'true');
    }

    // Dialog System
    function openDialog(character, charElement) {
        if (introOverlay.classList.contains('hidden') === false) return; // Don't open if intro is active

        currentCharacter = character;
        currentDialogIndex = 0;

        // Update UI
        dialogName.textContent = character.name;
        dialogRole.textContent = character.role;
        dialogAvatar.src = character.avatar;

        // Show Box
        dialogBox.classList.add('active');

        showDialogLine();

        // Mark as visited
        if (!visitedCharacters.includes(character.id)) {
            visitedCharacters.push(character.id);
            localStorage.setItem('visitedCharacters_about', JSON.stringify(visitedCharacters));
            charElement.classList.add('visited');
            updateProgressUI();
        }
    }

    function showDialogLine() {
        if (!currentCharacter) return;

        const text = currentCharacter.dialog[currentDialogIndex];

        if (currentDialogIndex === currentCharacter.dialog.length - 1) {
            btnNextDialog.style.display = 'none';
            btnCloseDialog.style.display = 'block';
        } else {
            btnNextDialog.style.display = 'block';
            btnCloseDialog.style.display = 'none';
        }

        typeWriter(text, dialogText);
    }

    btnNextDialog.addEventListener('click', () => {
        if (isTyping) {
            stopTypeWriter(currentCharacter.dialog[currentDialogIndex], dialogText);
        } else {
            currentDialogIndex++;
            if (currentDialogIndex < currentCharacter.dialog.length) {
                showDialogLine();
            }
        }
    });

    btnCloseDialog.addEventListener('click', () => {
        if (isTyping) {
            stopTypeWriter(currentCharacter.dialog[currentDialogIndex], dialogText);
        } else {
            dialogBox.classList.remove('active');
            currentCharacter = null;
        }
    });

    // Progress System
    function updateProgressUI() {
        const total = teamData.length;
        const current = visitedCharacters.length;
        const percentage = (current / total) * 100;

        progressCount.textContent = `${current}/${total}`;
        progressFill.style.width = `${percentage}%`;

        const btnResetProgress = document.getElementById('btn-reset-progress');
        if (btnResetProgress) {
            if (current === total) {
                btnResetProgress.style.display = 'inline-flex';
            } else {
                btnResetProgress.style.display = 'none';
            }
        }

        if (current === total && !localStorage.getItem('achievementShown_about')) {
            showAchievement();
        }
    }

    function showAchievement() {
        localStorage.setItem('achievementShown_about', 'true');
        setTimeout(() => {
            achievementPopup.classList.add('show');
            setTimeout(() => {
                achievementPopup.classList.remove('show');
            }, 5000);
        }, 1000);
    }

    // Reset Progress Functionality
    function resetProgress() {
        localStorage.removeItem('visitedCharacters_about');
        localStorage.removeItem('introFinished_about');
        localStorage.removeItem('achievementShown_about');

        visitedCharacters = [];
        introFinished = false;

        dialogBox.classList.remove('active');
        currentCharacter = null;

        renderCharacters();
        updateProgressUI();

        introOverlay.classList.remove('hidden');
        playIntro();
    }

    const btnResetProgress = document.getElementById('btn-reset-progress');
    if (btnResetProgress) {
        btnResetProgress.addEventListener('click', resetProgress);
    }
});
