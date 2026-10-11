<!-- TOPBAR -->

        <header class="topbar">

            <div class="page-title">
                <p><strong>TravelKu</strong>, staf pool</p>
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
                        {{ ($shiftInfo['pool_name'] ?? null) ?: 'Staf pool' }}
                    </span>

                </div>

            </div>

        </header>
