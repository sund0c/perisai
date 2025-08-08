<a href="{{ route('dashboard') }}" class="brand-link">
    <span class="brand-text font-weight-light">MATIK</span>
    @if(isset($tahunAktif))
        <span class="text-sm text-primary font-weight-bold d-block mt-1">
            <i class="fas fa-calendar-alt"></i> PERIODE {{ $tahunAktif->tahun }}
        </span>
    @endif
</a>
