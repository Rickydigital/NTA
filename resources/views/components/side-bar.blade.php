<!-- Left Sidebar Start -->
<div class="left-side-menu">

    <div class="sidebar-brand">
        <a href="{{ auth()->check() && auth()->user()->hasRole('Student') ? route('student.portal.dashboard') : route('dashboard') }}"
           style="display:flex; align-items:center; gap:10px;">
            <img class="brand-logo-full" src="{{ asset('app-assets/images/logo-light.png') }}" alt="NTA Portal" height="26">
            <img class="brand-logo-sm" src="{{ asset('app-assets/images/logo-sm.png') }}" alt="NTA Portal" height="32" style="display:none;">
        </a>
    </div>

    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                @auth
                    @if(auth()->user()->hasRole('Student'))

                        <li class="menu-title">Student Portal</li>

                        <li class="{{ request()->routeIs('student.portal.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('student.portal.dashboard') }}">
                                <i class="mdi mdi-view-dashboard-outline"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('student.portal.results.*') ? 'active' : '' }}">
                            <a href="{{ route('student.portal.results.index') }}">
                                <i class="mdi mdi-file-document-outline"></i>
                                <span>My Results</span>
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <a href="{{ route('profile.edit') }}">
                                <i class="mdi mdi-account-outline"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                    @else

                        <li class="menu-title">Navigation</li>

                        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}">
                                <i class="mdi mdi-view-dashboard-outline"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="mdi mdi-account-cog-outline"></i>
                                <span>Access Control</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('users.index') }}">Users</a>
                                </li>
                                <li>
                                    <a href="{{ route('roles.index') }}">Roles</a>
                                </li>
                                <li>
                                    <a href="{{ route('permissions.index') }}">Permissions</a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ request()->routeIs('programs.*') || request()->routeIs('program-levels.*') || request()->routeIs('courses.*') || request()->routeIs('academic-years.*') || request()->routeIs('exam-sessions.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="mdi mdi-book-education-outline"></i>
                                <span>Academic Setup</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('programs.index') }}">Programs</a>
                                </li>
                                <li>
                                    <a href="{{ route('program-levels.index') }}">Program Levels</a>
                                </li>
                                <li>
                                    <a href="{{ route('courses.index') }}">Courses</a>
                                </li>
                                <li>
                                    <a href="{{ route('academic-years.index') }}">Academic Years</a>
                                </li>
                                <li>
                                    <a href="{{ route('exam-sessions.index') }}">Exam Sessions</a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ request()->routeIs('students.*') || request()->routeIs('student-exam-numbers.*') || request()->routeIs('student-program-enrollments.*') || request()->routeIs('student-level-placements.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="mdi mdi-account-school-outline"></i>
                                <span>Student Management</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('students.index') }}">Students</a>
                                </li>
                                <li>
                                    <a href="{{ route('student-exam-numbers.index') }}">Student Exam Numbers</a>
                                </li>
                                <li>
                                    <a href="{{ route('student-program-enrollments.index') }}">Program Enrollments</a>
                                </li>
                                <li>
                                    <a href="{{ route('student-level-placements.index') }}">Level Placements</a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ request()->routeIs('grades.*') || request()->routeIs('gpa-classifications.*') || request()->routeIs('course-results.*') || request()->routeIs('exam-results.*') || request()->routeIs('progression-rules.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="mdi mdi-file-chart-outline"></i>
                                <span>Results Management</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('grades.index') }}">Grades</a>
                                </li>
                                <li>
                                    <a href="{{ route('gpa-classifications.index') }}">GPA Classifications</a>
                                </li>
                                <li>
                                    <a href="{{ route('course-results.index') }}">Course Results</a>
                                </li>
                                <li>
                                    <a href="{{ route('exam-results.index') }}">Exam Results</a>
                                </li>
                                <li>
                                    <a href="{{ route('progression-rules.index') }}">Progression Rules</a>
                                </li>
                            </ul>
                        </li>

                        <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="mdi mdi-chart-box-outline"></i>
                                <span>Reports</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="javascript:void(0);">Published Results</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Result Slips</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Performance Reports</a>
                                </li>
                            </ul>
                        </li>

                        <li class="menu-title">Account</li>

                        <li class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <a href="{{ route('profile.edit') }}">
                                <i class="mdi mdi-account-outline"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                    @endif
                @endauth

            </ul>
        </div>
    </div>
</div>
<!-- Left Sidebar End -->