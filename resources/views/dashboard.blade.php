@extends('components.main-layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'System overview and academic analytics')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Users</small>
                    <h3 class="mb-0">{{ number_format($stats['users']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Students</small>
                    <h3 class="mb-0">{{ number_format($stats['students']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Programs</small>
                    <h3 class="mb-0">{{ number_format($stats['programs']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Courses</small>
                    <h3 class="mb-0">{{ number_format($stats['courses']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Exam Sessions</small>
                    <h3 class="mb-0">{{ number_format($stats['exam_sessions']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Published Results</small>
                    <h3 class="mb-0">{{ number_format($stats['published_exam_results']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Students by Program</h5>
                    <div id="studentsByProgramChart" style="min-height: 340px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Course Results Status</h5>
                    <div id="courseResultsStatusChart" style="min-height: 340px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Students by Level</h5>
                    <div id="studentsByLevelChart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Exam Results Publication</h5>
                    <div id="examResultsPublicationChart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Recent Students</h5>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Reg No</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents as $student)
                                    <tr>
                                        <td>{{ $student->reg_no }}</td>
                                        <td>{{ $student->full_name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No records</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Recent Course Results</h5>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCourseResults as $row)
                                    <tr>
                                        <td>{{ $row->student?->full_name ?? '-' }}</td>
                                        <td>{{ $row->grade?->grade_code ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No records</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Recent Exam Results</h5>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>GPA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExamResults as $row)
                                    <tr>
                                        <td>{{ $row->student?->full_name ?? '-' }}</td>
                                        <td>{{ number_format($row->gpa, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No records</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const studentsByProgramOptions = {
            chart: {
                type: 'bar',
                height: 340,
                toolbar: { show: false }
            },
            series: [{
                name: 'Students',
                data: @json($chartData['studentsByProgramSeries'])
            }],
            xaxis: {
                categories: @json($chartData['studentsByProgramLabels'])
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    horizontal: false,
                    columnWidth: '45%'
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            }
        };

        const courseResultsStatusOptions = {
            chart: {
                type: 'donut',
                height: 340
            },
            series: @json($chartData['courseResultsStatusSeries']),
            labels: @json($chartData['courseResultsStatusLabels']),
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: true
            }
        };

        const studentsByLevelOptions = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false }
            },
            series: [{
                name: 'Students',
                data: @json($chartData['studentsByLevelSeries'])
            }],
            xaxis: {
                categories: @json($chartData['studentsByLevelLabels'])
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '50%'
                }
            }
        };

        const examResultsPublicationOptions = {
            chart: {
                type: 'pie',
                height: 320
            },
            series: @json($chartData['examResultsPublicationSeries']),
            labels: @json($chartData['examResultsPublicationLabels']),
            legend: {
                position: 'bottom'
            }
        };

        new ApexCharts(document.querySelector("#studentsByProgramChart"), studentsByProgramOptions).render();
        new ApexCharts(document.querySelector("#courseResultsStatusChart"), courseResultsStatusOptions).render();
        new ApexCharts(document.querySelector("#studentsByLevelChart"), studentsByLevelOptions).render();
        new ApexCharts(document.querySelector("#examResultsPublicationChart"), examResultsPublicationOptions).render();
    });
</script>
@endpush