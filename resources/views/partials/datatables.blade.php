{{-- Carga DataTables (jQuery + integración Bootstrap 5) y lo aplica a las tablas con clase .datatable --}}
@push('styles')
<link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function () {
        $('.datatable').DataTable({
            pageLength: 10,
            order: [],
            columnDefs: [{ targets: 'no-sort', orderable: false, searchable: false }],
            language: { url: 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/es-ES.json' },
        });

        // Confirmación antes de eliminar
        $(document).on('submit', 'form.form-eliminar', function (e) {
            if (!confirm('¿Seguro que deseas eliminar «' + this.dataset.nombre + '»? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
