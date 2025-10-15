@forelse($siswa as $i => $s)
    <tr>
    <td class="text-center">{{ $i + 1 }}</td>
    <td>{{ $s->name }}</td>
    <td class="text-center">{{ $s->username }}</td>
    <td class="text-center">{{ $s->kelas->name ?? '-' }}</td>
    <td class="text-center">{{ $s->rombel->name ?? '-' }}</td>
    <td class="text-center">{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
    <td class="text-center">{{ $s->orang_tua ?? '-' }}</td>
    <td class="text-center">{{ $s->kontak ?? '-' }}</td>
    </tr>
@empty
    <tr><td colspan="8" class="text-center">Tidak ada siswa untuk kelas ini.</td></tr>
@endforelse