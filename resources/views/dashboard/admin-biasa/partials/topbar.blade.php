<!-- TOPBAR -->

        <header class="topbar">

            <div class="page-title">

                <h1>
                    Dashboard Operasional (Layar Utama)
                </h1>

                <p>
                    Pusat pemantauan keberangkatan armada, validasi tiket, dan operasional loket.
                </p>

            </div>


            <div class="profile">

                <div class="avatar">

                    {{ strtoupper(
                        substr(
                            auth()->user()->name ?? 'S',
                            0,
                            1
                        )
                    ) }}

                </div>


                <div class="profile-info">

                    <strong>
                        {{ auth()->user()->name ?? 'Staff Operasional' }}
                    </strong>

                    <span>
                        {{ $shiftInfo['pool_name'] }}
                    </span>

                </div>

            </div>

        </header>
