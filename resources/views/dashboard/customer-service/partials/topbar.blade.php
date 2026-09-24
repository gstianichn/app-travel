<!-- TOPBAR -->

        <header class="topbar">

            <div>

                <div class="topbar-title">
                    Dashboard Customer Service
                </div>

                <div class="topbar-subtitle">
                    Kelola pelayanan dan kebutuhan pelanggan TravelKu
                </div>

            </div>


            <div class="profile">

                <div class="profile-avatar">

                    {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}

                </div>


                <div class="profile-info">

                    <span class="profile-name">
                        {{ auth()->user()->name ?? 'Customer Service' }}
                    </span>

                    <span class="profile-role">
                        Customer Service
                    </span>

                </div>

            </div>

        </header>
