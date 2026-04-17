<!-- Left Sidebar Start -->
<div class="left-side-menu">

    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:10px;">
            <img class="brand-logo-full" src="{{ asset('app-assets/images/logo-light.png') }}" alt="NTA Portal" height="26">
            <img class="brand-logo-sm" src="{{ asset('app-assets/images/logo-sm.png') }}" alt="NTA Portal" height="32" style="display:none;">
        </a>
    </div>

    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                <li class="menu-title">Navigation</li>

                @can('dashboard.view')
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @endcan

                @canany(['user.view', 'role.view', 'permission.view'])
                <li class="{{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <i class="mdi mdi-account-cog-outline"></i>
                        <span>Access Control</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        @can('user.view')
                        <li>
                            <a href="{{ route('users.index') }}">Users</a>
                        </li>
                        @endcan

                        @can('role.view')
                        <li>
                            <a href="{{ route('roles.index') }}">Roles</a>
                        </li>
                        @endcan

                        @can('permission.view')
                        <li>
                            <a href="{{ route('permissions.index') }}">Permissions</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                @canany([
                    'program.view',
                    'program-level.view',
                    'course.view',
                    'academic-year.view',
                    'exam-session.view'
                ])
                <li class="{{ request()->routeIs('programs.*') || request()->routeIs('program-levels.*') || request()->routeIs('courses.*') || request()->routeIs('academic-years.*') || request()->routeIs('exam-sessions.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <i class="mdi mdi-book-education-outline"></i>
                        <span>Academic Setup</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">

                        @can('program.view')
                        <li>
                            <a href="{{ route('programs.index') }}">Programs</a>
                        </li>
                        @endcan

                        @can('program-level.view')
                        <li>
                            <a href="{{ route('program-levels.index') }}">Program Levels</a>
                        </li>
                        @endcan

                        @can('course.view')
                        <li>
                            <a href="{{ route('courses.index') }}">Courses</a>
                        </li>
                        @endcan

                        @can('academic-year.view')
                        <li>
                           <a href="{{ route('academic-years.index') }}">Academic Years</a>
                        </li>
                        @endcan

                        @can('exam-session.view')
                        <li>
                            <a href="{{ route('exam-sessions.index') }}">Exam Sessions</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany

                @canany(['student.view', 'exam-number.view'])
                <li class="{{ request()->routeIs('students.*') || request()->routeIs('student-exam-numbers.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <i class="mdi mdi-account-school-outline"></i>
                        <span>Student Management</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">

                        @can('student.view')
                        <li>
                            <a href="{{ route('students.index') }}">Students</a>
                        </li>
                        <li>
                            <a href="{{ route('student-program-enrollments.index') }}">Program Enrollments</a>
                        </li>
                        <li>
                            <a href="{{ route('student-level-placements.index') }}">Level Placements</a>
                        </li>
                        @endcan
                        

                        @can('exam-number.view')
                        <li>
                            <a href="{{ route('student-exam-numbers.index') }}">Student Exam Numbers</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany

                @canany([
                    'grade.view',
                    'gpa-classification.view',
                    'result-entry.view',
                    'result-summary.view',
                    'progression-rule.view'
                ])
                <li class="{{ request()->routeIs('grades.*') || request()->routeIs('gpa-classifications.*') || request()->routeIs('course-results.*') || request()->routeIs('exam-results.*') || request()->routeIs('progression-rules.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <i class="mdi mdi-file-chart-outline"></i>
                        <span>Results Management</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">

                        @can('grade.view')
                        <li>
                            <a href="{{ route('grades.index') }}">Grades</a>
                        </li>
                        @endcan

                        @can('gpa-classification.view')
                        <li>
                            <a href="javascript:void(0);">GPA Classifications</a>
                        </li>
                        @endcan

                        @can('result-entry.view')
                        <li>
                            <a href="javascript:void(0);">Course Results</a>
                        </li>
                        @endcan

                        @can('result-summary.view')
                        <li>
                            <a href="javascript:void(0);">Exam Results</a>
                        </li>
                        @endcan

                        @can('progression-rule.view')
                        <li>
                            <a href="javascript:void(0);">Progression Rules</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany

                @canany(['report.view', 'report.generate'])
                <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <i class="mdi mdi-chart-box-outline"></i>
                        <span>Reports</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">

                        @can('report.view')
                        <li>
                            <a href="javascript:void(0);">Published Results</a>
                        </li>
                        @endcan

                        @can('report.generate')
                        <li>
                            <a href="javascript:void(0);">Result Slips</a>
                        </li>
                        @endcan

                        @can('report.generate')
                        <li>
                            <a href="javascript:void(0);">Performance Reports</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endcanany

                @can('student-portal.view')
                <li class="{{ request()->routeIs('student.portal.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <i class="mdi mdi-monitor-dashboard"></i>
                        <span>Student Portal</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="javascript:void(0);">My Results</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">My Profile</a>
                        </li>
                    </ul>
                </li>
                @endcan

                <li class="menu-title">Account</li>

                <li class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <a href="{{ route('profile.edit') }}">
                        <i class="mdi mdi-account-outline"></i>
                        <span>My Profile</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- Left Sidebar End -->