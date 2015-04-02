Summary: nethserver httpd configuration
Name: nethserver-httpd
Version: 2.4.0
Release: 1%{?dist}
License: GPL
Source: %{name}-%{version}.tar.gz
BuildArch: noarch
URL: %{url_prefix}/%{name} 

BuildRequires: nethserver-devtools >= 1.1.0-5

AutoReq: no
Requires: nethserver-base >= 1.1.0-2
Requires: httpd, mod_ssl 

%description
NethServer httpd configuration (Apache)

%prep
%setup

%build
%{makedocs}
perl createlinks

%install
rm -rf $RPM_BUILD_ROOT
(cd root   ; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)

%{genfilelist} $RPM_BUILD_ROOT > %{name}-%{version}-filelist
echo "%config /etc/httpd/conf/ibays.htpasswd" >> %{name}-%{version}-filelist
echo "%doc COPYING" >> %{name}-%{version}-filelist

%clean 
rm -rf $RPM_BUILD_ROOT

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)

%changelog
* Thu Apr 02 2015 Davide Principi <davide.principi@nethesis.it> - 2.4.0-1
- Require SSL encrypted connection for shared folder - Feature #3102 [NethServer]
- Allow .htaccess and write permissions overrides - Feature #3097 [NethServer]
- Validation error setting ibay password - Bug #3094 [NethServer]
- nethserver-devbox replacements - Feature #3009 [NethServer]

* Wed Oct 22 2014 Davide Principi <davide.principi@nethesis.it> - 2.3.3-1.ns6
- Protection against POODLE SSLv3 Vulnerability - Bug #2921 [NethServer]

* Thu Oct 02 2014 Davide Principi <davide.principi@nethesis.it> - 2.3.2-1.ns6
- certificate-update event should restart httpd - Bug #2856 [NethServer]
- HTTP Forbidden access to PHP webapp after migration - Bug #2823 [NethServer]

* Fri Jun 06 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.1-1.ns6
- Preserve IP host name in HTTP requests - Enhancement #2758

* Wed Feb 26 2014 Davide Principi <davide.principi@nethesis.it> - 2.3.0-1.ns6
- httpd FollowSymLinks and DirectoryIndex directives - Enhancement #2649 [NethServer]
- "Missing read permssion"  warning from httpd.conf template - Enhancement #2637 [NethServer]
- Simplify Shared Folder/Web access UI module - Enhancement #2631 [NethServer]

* Wed Feb 05 2014 Davide Principi <davide.principi@nethesis.it> - 2.2.2-1.ns6
- RST format for help files - Enhancement #2627 [NethServer]
- NethCamp 2014 - Task #2618 [NethServer]
- Lib: synchronize service status prop and chkconfig - Feature #2067 [NethServer]

* Thu Jul 18 2013 Davide Principi <davide.principi@nethesis.it> - 2.2.1-1.ns6
- Version fix - Enhancement #1937 [NethServer]

* Fri Jun 21 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.1.1-1.ns6
- Httpd: load all standard CentOS modules #1937
- Httpd: add cgi support #1918

* Tue Apr 30 2013 Davide Principi <davide.principi@nethesis.it> - 2.2.0-1.ns6
- Full automatic package install/upgrade/uninstall support #1870 #1872 #1874
- Use ibays/system-acls template to configure apache permissions #1734 #1891 

* Tue Mar 26 2013 Davide Principi <davide.principi@nethesis.it> - 2.1.0-1.ns6
- httpd.conf template: module fragment is expanded before base fragment. *.conf, *.ibay and *.vhost Include must now check if a module is loaded (IfModule !modname). Refs #1793 
- Bugfixes #1792 #1791

* Tue Mar 19 2013 Davide Principi <davide.principi@nethesis.it> - 2.0.0-1.ns6
- Grant read access to Apache web server by adding Posix ACLs. Refs #1734
- Use certificate management from nethserver-base. Refs #1723
- Migration support. Refs #1690
- Support for virtual hosts and ibays profiles. Refs #1690
- Emit log warnings if some misconfiguration is detected. Refs #1690
- *.spec.in: fixed %files section; use url_prefix macro in URL tag; 
  set minimum version requirements. Refs #1690 #1653 #1654

* Thu Jan 31 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.1-1.ns6
- Implemented nethserver-base certificate management


