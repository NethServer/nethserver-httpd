Name: nethserver-virtualhosts
Summary: Virtual hosts configuration
Version: 1.0.6
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source: %{name}-%{version}.tar.gz

BuildArch: noarch
Requires: nethserver-vsftpd, nethserver-httpd
BuildRequires: perl, perl(File::Path), nethserver-devtools

%description
Virtual hosts are public HTTP directories accessible using FTP.

%prep
%setup

%post

%build
%{makedocs}
perl createlinks

%install
rm -rf %{buildroot}
(cd root   ; find . -depth -print | cpio -dump %{buildroot})
%{genfilelist} %{buildroot} > %{name}-%{version}-%{release}-filelist

%clean 
rm -rf %{buildroot}

%files -f %{name}-%{version}-%{release}-filelist
%defattr(-,root,root)
%doc COPYING
%dir %{_nseventsdir}/%{name}-update
%config(noreplace) %ghost %attr (0644,root,root) %{_sysconfdir}/httpd/conf.d/virtualhosts.conf

%changelog
* Fri Apr 20 2018 Stephane de Labrusse <stephdl@de-labrusse.fr> - 1.0.6-1
- Description prop of host key is '1' - Bug NethServer/dev#5462
- The key is not verified in nethserver-virtualhosts - Bug NethServer/dev#5461
- Automated RPM builds - NethServer/dev#5393

* Thu Jul 06 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.5-1
- Virtual hosts: add indexes option - NethServer/dev#5324

* Wed Jun 07 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.4-1
-  Same FQDN can be set to different virtualhosts - Bug NethServer/dev#5303

* Tue May 30 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.3-1
- Upgrade from NS 6 via backup and restore - NethServer/dev#5234

* Wed Nov 02 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1
- Virtual host inline help does not include plugins - NethServer/dev#5138

* Thu Sep 01 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1
- Apache vhost-default template expansion - NethServer/dev#5088

* Thu Jul 07 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- First NS7 release

