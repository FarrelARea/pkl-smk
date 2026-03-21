<!-- Header Section -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">System Overview</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Manage academic institutional partnerships and track internship deployment metrics across the network.
        </p>
    </div>
    <div class="flex gap-3">
        <button class="px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">
            Export Report
        </button>
        <a href="/admin/schools" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Add New School
        </a>
    </div>
</header>

<!-- Stats Grid (Bento Style) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <a href="/admin/schools" class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] col-span-1 border border-outline-variant/10 hover:shadow-lg transition-shadow block">
        <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Total Schools</p>
        <div class="flex items-baseline gap-2">
            <span id="stat-schools" class="text-4xl font-extrabold text-primary">—</span>
        </div>
        <div class="mt-4 h-1.5 w-full bg-primary-fixed rounded-full overflow-hidden">
            <div class="h-full bg-primary w-[75%] rounded-full"></div>
        </div>
    </a>
    <a href="/admin/students" class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] col-span-1 border border-outline-variant/10 hover:shadow-lg transition-shadow block">
        <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Total Students</p>
        <div class="flex items-baseline gap-2">
            <span id="stat-students" class="text-4xl font-extrabold text-on-surface">—</span>
        </div>
        <div class="mt-4 h-1.5 w-full bg-secondary-fixed rounded-full overflow-hidden">
            <div class="h-full bg-secondary w-[60%] rounded-full"></div>
        </div>
    </a>
    <a href="/admin/internships" class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] col-span-1 border border-outline-variant/10 hover:shadow-lg transition-shadow block">
        <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Active Internships</p>
        <div class="flex items-baseline gap-2">
            <span id="stat-internships" class="text-4xl font-extrabold text-tertiary">—</span>
        </div>
        <div class="mt-4 h-1.5 w-full bg-tertiary-fixed rounded-full overflow-hidden">
            <div class="h-full bg-tertiary w-[88%] rounded-full"></div>
        </div>
    </a>
    <a href="/admin/companies" class="bg-primary-container p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.1)] col-span-1 text-white relative overflow-hidden group hover:shadow-lg transition-shadow block">
        <div class="relative z-10">
            <p class="text-[0.7rem] font-bold text-primary-fixed uppercase tracking-widest mb-1">Companies</p>
            <div class="flex items-baseline gap-2">
                <span id="stat-companies" class="text-4xl font-extrabold">—</span>
            </div>
            <p class="mt-4 text-xs text-primary-fixed/80">Partner organizations</p>
        </div>
        <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl opacity-10 group-hover:scale-110 transition-transform">business</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Schools Table Section -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
            <div class="p-6 flex justify-between items-center bg-white border-b border-surface-container">
                <h2 class="text-xl font-bold tracking-tight font-headline">Institutional Partners</h2>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                    <input id="school-search" class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Search schools..." type="text">
                </div>
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low">
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Institution</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Active Students</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="schools-table" class="divide-y divide-surface-container">
                    <tr class="hover:bg-surface-container-high transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center">
                                    <span class="material-symbols-outlined text-on-secondary-container">account_balance</span>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface">Global Tech Institute</p>
                                    <p class="text-xs text-on-surface-variant">San Francisco, CA</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5"><p class="text-sm font-medium">142 Students</p></td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-full text-[0.65rem] font-bold uppercase tracking-widest">Active</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-sm">edit</span></button>
                                <button class="p-2 text-on-surface-variant hover:text-error transition-colors"><span class="material-symbols-outlined text-sm">delete</span></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-high transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center">
                                    <span class="material-symbols-outlined text-on-secondary-container">account_balance</span>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface">Design Academy East</p>
                                    <p class="text-xs text-on-surface-variant">New York, NY</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5"><p class="text-sm font-medium">89 Students</p></td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-full text-[0.65rem] font-bold uppercase tracking-widest">Active</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-sm">edit</span></button>
                                <button class="p-2 text-on-surface-variant hover:text-error transition-colors"><span class="material-symbols-outlined text-sm">delete</span></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-high transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center">
                                    <span class="material-symbols-outlined text-on-secondary-container">account_balance</span>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface">Maritime University</p>
                                    <p class="text-xs text-on-surface-variant">Seattle, WA</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5"><p class="text-sm font-medium">56 Students</p></td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 bg-surface-container-highest text-on-surface-variant rounded-full text-[0.65rem] font-bold uppercase tracking-widest">Pending</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-sm">edit</span></button>
                                <button class="p-2 text-on-surface-variant hover:text-error transition-colors"><span class="material-symbols-outlined text-sm">delete</span></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-high transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center">
                                    <span class="material-symbols-outlined text-on-secondary-container">account_balance</span>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface">Central Bio-Sciences</p>
                                    <p class="text-xs text-on-surface-variant">Chicago, IL</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5"><p class="text-sm font-medium">211 Students</p></td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-full text-[0.65rem] font-bold uppercase tracking-widest">Active</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-sm">edit</span></button>
                                <button class="p-2 text-on-surface-variant hover:text-error transition-colors"><span class="material-symbols-outlined text-sm">delete</span></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Sidebar Components -->
    <div class="space-y-8">
        <!-- Register New School Card -->
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] border border-outline-variant/10">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2 font-headline">
                <span class="material-symbols-outlined text-primary">add_circle</span>
                Register New School
            </h3>
            <form class="space-y-4">
                <div>
                    <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Institution Name</label>
                    <input class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
                </div>
                <div>
                    <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Administrator Email</label>
                    <input class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="email">
                </div>
                <button class="w-full py-3 primary-gradient text-white rounded-lg font-bold text-sm shadow-md active:scale-95 transition-transform" type="submit">
                    Create Partner Profile
                </button>
            </form>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] border border-outline-variant/10">
            <h3 class="font-bold text-lg mb-6 flex items-center gap-2 font-headline">
                <span class="material-symbols-outlined text-primary">history</span>
                Recent Activity
            </h3>
            <div class="space-y-6 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-surface-container">
                <div class="relative pl-8">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-primary-fixed flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-[0.9rem] text-primary" style="font-variation-settings: 'FILL' 1">check_circle</span>
                    </div>
                    <p class="text-sm font-semibold">New school onboarded</p>
                    <p class="text-xs text-on-surface-variant">Global Tech Institute joined the network.</p>
                    <p class="text-[0.6rem] font-bold text-outline mt-1 uppercase">2 hours ago</p>
                </div>
                <div class="relative pl-8">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-secondary-fixed flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-[0.9rem] text-secondary">assignment</span>
                    </div>
                    <p class="text-sm font-semibold">50 Applications Processed</p>
                    <p class="text-xs text-on-surface-variant">Bulk evaluation complete for Design Academy.</p>
                    <p class="text-[0.6rem] font-bold text-outline mt-1 uppercase">5 hours ago</p>
                </div>
                <div class="relative pl-8">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-tertiary-fixed flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-[0.9rem] text-tertiary">description</span>
                    </div>
                    <p class="text-sm font-semibold">Report Generated</p>
                    <p class="text-xs text-on-surface-variant">Monthly compliance report is ready for review.</p>
                    <p class="text-[0.6rem] font-bold text-outline mt-1 uppercase">Yesterday</p>
                </div>
                <div class="relative pl-8">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-error-container flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-[0.9rem] text-error">warning</span>
                    </div>
                    <p class="text-sm font-semibold">Document Expired</p>
                    <p class="text-xs text-on-surface-variant">Central Bio-Sciences insurance needs renewal.</p>
                    <p class="text-[0.6rem] font-bold text-outline mt-1 uppercase">2 days ago</p>
                </div>
            </div>
        </div>
    </div>
</div>
