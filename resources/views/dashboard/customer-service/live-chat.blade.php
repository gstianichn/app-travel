<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Live Chat - TravelKu</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --green: #087F5B;
            --green-dark: #07543F;
            --green-light: #E8F5F0;

            --white: #FFFFFF;
            --page: #F5F7F6;

            --text: #111827;
            --text-secondary: #64748B;

            --border: #DDE5E2;

            --danger: #DC2626;

            --sidebar-width: 260px;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--page);
            color: var(--text);
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;

            width: var(--sidebar-width);
            height: 100vh;

            background: var(--white);

            border-right: 1px solid var(--border);

            display: flex;
            flex-direction: column;

            z-index: 1000;
        }

        .sidebar-header {
            height: 86px;

            padding: 0 25px;

            display: flex;
            align-items: center;

            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;

            text-decoration: none;
        }

        .brand-icon {
            width: 42px;
            height: 42px;

            border-radius: 10px;

            background: var(--green);

            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
            font-weight: 700;
        }

        .brand-title {
            color: var(--green);

            font-size: 20px;
            font-weight: 700;
        }

        .brand-subtitle {
            display: block;

            color: var(--text-secondary);

            font-size: 11px;

            margin-top: 2px;
        }

        .menu {
            flex: 1;

            padding: 25px 15px;

            overflow-y: auto;
        }

        .menu-title {
            padding: 0 12px 12px;

            color: #94A3B8;

            font-size: 11px;
            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1px;
        }

        .menu-list {
            list-style: none;
        }

        .menu-item {
            margin-bottom: 5px;
        }

        .menu-link {
            display: flex;
            align-items: center;

            gap: 12px;

            padding: 12px 13px;

            border-radius: 9px;

            color: var(--text-secondary);

            text-decoration: none;

            font-size: 14px;
            font-weight: 500;

            transition: .2s ease;
        }

        .menu-link:hover {
            background: var(--green-light);

            color: var(--green);
        }

        .menu-link.active {
            background: var(--green);

            color: white;
        }

        .menu-icon {
            width: 20px;

            display: flex;
            justify-content: center;
        }

        .sidebar-footer {
            padding: 15px;

            border-top: 1px solid var(--border);
        }

        .logout-button {
            width: 100%;

            border: none;

            background: transparent;

            color: var(--danger);

            display: flex;
            align-items: center;

            gap: 12px;

            padding: 12px 13px;

            border-radius: 9px;

            font-size: 14px;

            cursor: pointer;
        }

        .logout-button:hover {
            background: #FEF2F2;
        }


        /* MAIN */

        .main {
            margin-left: var(--sidebar-width);

            min-height: 100vh;
        }

        .topbar {
            height: 86px;

            padding: 0 35px;

            background: white;

            border-bottom: 1px solid var(--border);

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-title {
            font-size: 22px;

            font-weight: 700;
        }

        .topbar-subtitle {
            margin-top: 3px;

            color: var(--text-secondary);

            font-size: 13px;
        }

        .profile {
            display: flex;
            align-items: center;

            gap: 12px;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: var(--green-light);

            color: var(--green);

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 700;
        }

        .profile-name {
            font-size: 14px;
            font-weight: 600;
        }

        .profile-role {
            display: block;

            color: var(--text-secondary);

            font-size: 11px;

            margin-top: 2px;
        }


        /* CONTENT */

        .content {
            padding: 30px 35px;
        }


        /* CHAT LAYOUT */

        .chat-container {
            display: grid;

            grid-template-columns: 280px minmax(0, 1fr) 280px;

            height: calc(100vh - 146px);

            min-height: 600px;

            background: white;

            border: 1px solid var(--border);

            border-radius: 14px;

            overflow: hidden;
        }


        /* CHAT LIST */

        .chat-list {
            border-right: 1px solid var(--border);

            display: flex;
            flex-direction: column;
        }

        .chat-list-header {
            padding: 20px;

            border-bottom: 1px solid var(--border);
        }

        .chat-list-title {
            font-size: 16px;

            font-weight: 700;
        }

        .search {
            margin-top: 12px;

            width: 100%;

            padding: 10px 12px;

            border: 1px solid var(--border);

            border-radius: 8px;

            outline: none;
        }

        .chat-users {
            overflow-y: auto;
        }

        .chat-user {
            padding: 15px;

            display: flex;

            gap: 12px;

            border-bottom: 1px solid #EEF2F1;

            cursor: pointer;
        }

        .chat-user:hover,
        .chat-user.active {
            background: var(--green-light);
        }

        .user-avatar {
            width: 42px;
            height: 42px;

            flex-shrink: 0;

            border-radius: 50%;

            background: var(--green-light);

            color: var(--green);

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 700;
        }

        .user-info {
            min-width: 0;
        }

        .user-name {
            font-size: 13px;

            font-weight: 600;
        }

        .user-message {
            margin-top: 4px;

            color: var(--text-secondary);

            font-size: 11px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }


        /* CONVERSATION */

        .conversation {
            display: flex;

            flex-direction: column;
        }

        .conversation-header {
            padding: 18px 22px;

            border-bottom: 1px solid var(--border);

            display: flex;

            align-items: center;

            gap: 12px;
        }

        .online {
            width: 9px;
            height: 9px;

            border-radius: 50%;

            background: #16A34A;
        }

        .conversation-name {
            font-size: 15px;

            font-weight: 700;
        }

        .conversation-status {
            margin-top: 3px;

            color: #16A34A;

            font-size: 11px;
        }

        .messages {
            flex: 1;

            padding: 25px;

            overflow-y: auto;

            background: #F8FAF9;
        }

        .message {
            max-width: 70%;

            margin-bottom: 16px;
        }

        .message.customer {
            margin-right: auto;
        }

        .message.cs {
            margin-left: auto;
        }

        .message-bubble {
            padding: 11px 14px;

            border-radius: 12px;

            font-size: 13px;

            line-height: 1.5;
        }

        .customer .message-bubble {
            background: white;

            border: 1px solid var(--border);
        }

        .cs .message-bubble {
            background: var(--green);

            color: white;
        }

        .message-time {
            margin-top: 4px;

            color: var(--text-secondary);

            font-size: 10px;
        }

        .message.cs .message-time {
            text-align: right;
        }


        /* INPUT */

        .message-input {
            padding: 15px;

            border-top: 1px solid var(--border);

            display: flex;

            gap: 10px;
        }

        .attach-button {
            width: 42px;

            border: 1px solid var(--border);

            background: white;

            border-radius: 8px;

            cursor: pointer;
        }

        .message-input input[type="text"] {
            flex: 1;

            border: 1px solid var(--border);

            border-radius: 8px;

            padding: 11px 13px;

            outline: none;
        }

        .send-button {
            border: none;

            padding: 0 18px;

            border-radius: 8px;

            background: var(--green);

            color: white;

            font-weight: 600;

            cursor: pointer;
        }

        .send-button:hover {
            background: var(--green-dark);
        }


        /* BOOKING CONTEXT */

        .booking-context {
            border-left: 1px solid var(--border);

            padding: 20px;

            overflow-y: auto;
        }

        .context-title {
            font-size: 16px;

            font-weight: 700;

            margin-bottom: 20px;
        }

        .context-card {
            padding: 15px;

            background: var(--green-light);

            border-radius: 10px;

            margin-bottom: 15px;
        }

        .context-label {
            color: var(--text-secondary);

            font-size: 10px;

            margin-bottom: 4px;
        }

        .context-value {
            font-size: 13px;

            font-weight: 600;
        }

        .history-title {
            margin-top: 25px;

            margin-bottom: 12px;

            font-size: 14px;

            font-weight: 700;
        }

        .history-item {
            padding: 10px 0;

            border-bottom: 1px solid #EEF2F1;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-name {
            font-size: 12px;

            font-weight: 600;
        }

        .history-date {
            margin-top: 3px;

            color: var(--text-secondary);

            font-size: 10px;
        }

        .escalate-button {
            width: 100%;

            margin-top: 25px;

            padding: 11px;

            border: none;

            border-radius: 8px;

            background: #FEF2F2;

            color: var(--danger);

            font-weight: 600;

            cursor: pointer;
        }

        .escalate-button:hover {
            background: #FEE2E2;
        }


        /* RESPONSIVE */

        @media(max-width: 1200px) {

            .chat-container {
                grid-template-columns: 240px minmax(0, 1fr);
            }

            .booking-context {
                display: none;
            }

        }

        @media(max-width: 800px) {

            .sidebar {
                width: 0;

                overflow: hidden;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 0 20px;
            }

            .content {
                padding: 20px;
            }

            .chat-container {
                grid-template-columns: 1fr;

                height: auto;
            }

            .chat-list {
                display: none;
            }

            .conversation {
                min-height: 650px;
            }

            .profile-info {
                display: none;
            }

        }

    </style>

</head>


<body>


<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="sidebar-header">

        <a
            href="{{ route('dashboard.customer-service.index') }}"
            class="brand"
        >

            <div class="brand-icon">
                T
            </div>

            <div>

                <div class="brand-title">
                    TravelKu
                </div>

                <span class="brand-subtitle">
                    Management System
                </span>

            </div>

        </a>

    </div>


    <nav class="menu">

        <div class="menu-title">
            Menu Utama
        </div>

        <ul class="menu-list">

            <li class="menu-item">

                <a
                    href="{{ route('dashboard.customer-service.index') }}"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ⌂
                    </span>

                    Dashboard

                </a>

            </li>


            <li class="menu-item">

                <a
                    href="{{ route('dashboard.customer-service.live-chat') }}"
                    class="menu-link active"
                >

                    <span class="menu-icon">
                        💬
                    </span>

                    Layanan Pelanggan

                </a>

            </li>


            <li class="menu-item">

                <a
                    href="#"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ▣
                    </span>

                    Pemesanan

                </a>

            </li>


            <li class="menu-item">

                <a
                    href="#"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ♙
                    </span>

                    Pelanggan

                </a>

            </li>


            <li class="menu-item">

                <a
                    href="#"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ◷
                    </span>

                    Jadwal Travel

                </a>

            </li>


            <li class="menu-item">

                <a
                    href="#"
                    class="menu-link"
                >

                    <span class="menu-icon">
                        ▤
                    </span>

                    Laporan

                </a>

            </li>

        </ul>

    </nav>


    <div class="sidebar-footer">

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="logout-button"
            >

                <span class="menu-icon">
                    ⇥
                </span>

                Logout

            </button>

        </form>

    </div>

</aside>


<!-- MAIN -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">

        <div>

            <div class="topbar-title">
                Live Chat
            </div>

            <div class="topbar-subtitle">
                Layani pelanggan TravelKu secara langsung
            </div>

        </div>


        <div class="profile">

            <div class="profile-avatar">

                {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}

            </div>

            <div class="profile-info">

                <div class="profile-name">
                    {{ auth()->user()->name ?? 'Customer Service' }}
                </div>

                <span class="profile-role">
                    Customer Service
                </span>

            </div>

        </div>

    </header>


    <!-- CONTENT -->

    <section class="content">


        <div class="chat-container">


            <!-- DAFTAR CHAT -->

            <aside class="chat-list">

                <div class="chat-list-header">

                    <div class="chat-list-title">
                        Percakapan
                    </div>

                    <input
                        type="text"
                        class="search"
                        placeholder="Cari pelanggan..."
                    >

                </div>


                <div class="chat-users">


                    <div class="chat-user active">

                        <div class="user-avatar">
                            A
                        </div>

                        <div class="user-info">

                            <div class="user-name">
                                Andi
                            </div>

                            <div class="user-message">
                                Halo, saya ingin menanyakan booking...
                            </div>

                        </div>

                    </div>


                    <div class="chat-user">

                        <div class="user-avatar">
                            B
                        </div>

                        <div class="user-info">

                            <div class="user-name">
                                Budi
                            </div>

                            <div class="user-message">
                                Apakah jadwal Bandung tersedia?
                            </div>

                        </div>

                    </div>


                    <div class="chat-user">

                        <div class="user-avatar">
                            S
                        </div>

                        <div class="user-info">

                            <div class="user-name">
                                Sinta
                            </div>

                            <div class="user-message">
                                Saya ingin mengubah jadwal...
                            </div>

                        </div>

                    </div>


                </div>

            </aside>


            <!-- PERCAKAPAN -->

            <section class="conversation">


                <div class="conversation-header">

                    <div class="user-avatar">
                        A
                    </div>

                    <div>

                        <div class="conversation-name">
                            Andi
                        </div>

                        <div class="conversation-status">
                            ● Online
                        </div>

                    </div>

                </div>


                <div class="messages">


                    <div class="message customer">

                        <div class="message-bubble">
                            Halo, saya ingin menanyakan status booking saya.
                        </div>

                        <div class="message-time">
                            10:20
                        </div>

                    </div>


                    <div class="message cs">

                        <div class="message-bubble">
                            Halo Andi, tentu. Saya bantu cek booking Anda.
                        </div>

                        <div class="message-time">
                            10:21
                        </div>

                    </div>


                    <div class="message customer">

                        <div class="message-bubble">
                            Nomor booking saya TRV-001.
                        </div>

                        <div class="message-time">
                            10:22
                        </div>

                    </div>


                    <div class="message cs">

                        <div class="message-bubble">
                            Baik, booking TRV-001 sedang dalam proses
                            konfirmasi.
                        </div>

                        <div class="message-time">
                            10:23
                        </div>

                    </div>


                </div>


                <!-- INPUT CHAT -->

                <form class="message-input">

                    <input
                        type="file"
                        id="file"
                        hidden
                    >

                    <label
                        for="file"
                        class="attach-button"
                        style="
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        "
                    >
                        📎
                    </label>

                    <input
                        type="text"
                        placeholder="Tulis pesan..."
                    >

                    <button
                        type="submit"
                        class="send-button"
                    >
                        Kirim
                    </button>

                </form>

            </section>


            <!-- KONTEKS BOOKING -->

            <aside class="booking-context">


                <div class="context-title">
                    Konteks Booking
                </div>


                <div class="context-card">

                    <div class="context-label">
                        Kode Booking
                    </div>

                    <div class="context-value">
                        TRV-001
                    </div>

                </div>


                <div class="context-card">

                    <div class="context-label">
                        Nama Pelanggan
                    </div>

                    <div class="context-value">
                        Andi
                    </div>

                </div>


                <div class="context-card">

                    <div class="context-label">
                        Rute
                    </div>

                    <div class="context-value">
                        Jakarta → Bandung
                    </div>

                </div>


                <div class="context-card">

                    <div class="context-label">
                        Status Booking
                    </div>

                    <div class="context-value">
                        Menunggu Konfirmasi
                    </div>

                </div>


                <div class="history-title">
                    Riwayat Chat
                </div>


                <div class="history-item">

                    <div class="history-name">
                        Pertanyaan booking
                    </div>

                    <div class="history-date">
                        17 September 2026
                    </div>

                </div>


                <div class="history-item">

                    <div class="history-name">
                        Perubahan jadwal
                    </div>

                    <div class="history-date">
                        15 September 2026
                    </div>

                </div>


                <button
                    type="button"
                    class="escalate-button"
                >
                    ⚠ Eskalasi ke Admin
                </button>


            </aside>

        </div>

    </section>

</main>

</body>

</html>
