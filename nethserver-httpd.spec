Summary: nethserver httpd configuration
Name: nethserver-httpd
Version: 3.6.2
Release: 1%{?dist}
License: GPLv3
Source: %{name}-%{version}.tar.gz
Source1: %{name}-ui.tar.gz
BuildArch: noarch
URL: %{url_prefix}/%{name}
BuildRequires: nethserver-devtools
Requires: nethserver-base
Requires: httpd, mod_ssl
%description
NethServer httpd configuration (Apache)
%files -f default.lst
%defattr(-,root,root)
%doc COPYING
%doc README.rst
%dir %{_nseventsdir}/%{name}-update
%attr(0644,root,root) %ghost %{_sysconfdir}/httpd/conf.d/nethserver.conf
%attr(0644,root,root) %ghost %{_sysconfdir}/httpd/conf.d/virtualhosts.conf
%attr(0644,root,root) %ghost %{_sysconfdir}/httpd/conf.d/default-virtualhost.inc
%attr(0440,root,root) /etc/sudoers.d/50_nsapi_nethserver_httpd
%attr(0440,root,root) /etc/sudoers.d/30_httpd_app_launcher
/usr/share/cockpit/nethserver/applications/%{name}.json
/usr/libexec/nethserver/api/%{name}/
/usr/share/cockpit/%{name}/

%package proxypass
Summary: Reverse proxy configuration and UI
Requires: %{name} >= %{version}-%{release}
%description proxypass
NethServer reverse proxy configuration (Apache)
%files proxypass -f proxypass.lst
%defattr(-,root,root)
%doc COPYING
%doc README.rst
%dir %{_nseventsdir}/%{name}-proxypass-update

%package virtualhosts
Summary: Reverse proxy configuration and UI
Requires: %{name} >= %{version}-%{release}
BuildArch: noarch
Requires: nethserver-vsftpd
Obsoletes: nethserver-virtualhosts < 2
Provides: nethserver-virtualhosts = %{version}-%{release}
%description virtualhosts
Virtual hosts are public HTTP directories accessible using FTP.
%files virtualhosts -f virtualhosts.lst
%defattr(-,root,root)
%doc COPYING
%doc README.rst
%dir %{_nseventsdir}/%{name}-virtualhosts-update

%prep
%setup

%build
mkdir -p default/%{_nsdbconfdir}/proxypass/{migrate,force,defaults}
mkdir -p virtualhosts/%{_nseventsdir}/%{name}-virtualhosts-update
mkdir -p proxypass/%{_nseventsdir}/%{name}-proxypass-update
sed -i 's/_RELEASE_/%{version}/' %{name}.json

for package in default proxypass virtualhosts; do
    if [[ -f createlinks-${package} ]]; then
        # Hack around createlinks output dir prefix, hardcoded as "root/":
        rm -f root
        ln -sf ${package} root
        perl createlinks-${package}
    fi
    ( cd ${package} ; %{makedocs} )
    %{genfilelist} ${PWD}/${package} | \
          grep -v -e '/etc/sudoers.d/' \
          >> ${package}.lst
    # !!! Do not create any file or directory after genfilelist invocation !!!
done


%install
mkdir -p %{buildroot}/usr/share/cockpit/%{name}/
mkdir -p %{buildroot}/usr/share/cockpit/nethserver/applications/
mkdir -p %{buildroot}/usr/libexec/nethserver/api/%{name}/
tar xf %{SOURCE1} -C %{buildroot}/usr/share/cockpit/%{name}/
cp -a %{name}.json %{buildroot}/usr/share/cockpit/nethserver/applications/
cp -a api/* %{buildroot}/usr/libexec/nethserver/api/%{name}/

for package in default proxypass virtualhosts; do
    (cd ${package}; find . -depth -print | cpio -dump %{buildroot})
done


%changelog
* Mon Oct 28 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.6.2-1
- Logs page in Cockpit - Bug NethServer/dev#5866

* Thu Oct 10 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.6.1-1
- Improve app launcher loading - NethServer/dev#5859

* Tue Oct 01 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.6.0-1
- Sudoers based authorizations for Cockpit UI - NethServer/dev#5805

* Tue Sep 03 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.5.0-1
- Cockpit. List correct application version - Nethserver/dev#5819
- Quick httpd restart - NethServer/dev#5816

* Mon Aug 26 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.4.0-1
- Cockpit: vsftpd Log is missing - Bug NethServer/dev#5812
- Cockpit: link apps in home page - NethServer/dev#5806

* Mon Aug 19 2019 Stephane de Labrusse <stephdl@de-labrusse.fr> - 3.3.1-1
- Display the creation button and the user list if ftp is installed

* Wed Jun 26 2019 Davide Principi <davide.principi@nethesis.it> - 3.3.0-1
- Web server Cockpit UI - NethServer/dev#5778

* Thu Aug 09 2018 Davide Principi <davide.principi@nethesis.it> - 3.2.7-1
- Enhancement: (un)mask password fields - NethServer/dev#5554

* Thu Jul 12 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.6-1
- Virtualhosts: freepbx wrongly included inside the data backup - Bug NethServer/dev#5540

* Tue Jun 26 2018 Davide Principi <davide.principi@nethesis.it> - 3.2.5-1
- Allow Elliptic Curve Criptography (ECC) certificate - NethServer/dev#5509

* Thu Jun 07 2018 Davide Principi <davide.principi@nethesis.it> - 3.2.4-1
- Virtualhosts: "SSLEngine on" added only with non-default certificate - Bug NethServer/dev#5515

* Mon May 21 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.3-1
- Remove direct link to server manager from default index page - Thanks to danb35

* Wed May 16 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.2-1
- Rebuild for NS 7.5 - NethServer/dev#5490

* Tue May 08 2018 Stephane de Labrusse <stephdl@de-labrusse.fr> - 3.2.1-1
- Reverse proxy for virtualhost - NethServer/dev#5454
- One rpm to build nethserver-httpd, nethserver-httpd-virtualhost, nethserver-httpd-proxypass

* Fri Apr 20 2018 Stephane de Labrusse <stephdl@de-labrusse.fr> - 1.0.6-1
- Description prop of host key is '1' - Bug NethServer/dev#5462
- The key is not verified in nethserver-virtualhosts - Bug NethServer/dev#5461
- Automated RPM builds - NethServer/dev#5393

* Tue Apr 03 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.2.0-1
- Hardening TLS policy 2018-03-30 - NethServer/dev#5438

* Thu Jul 06 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.5-1
- Virtual hosts: add indexes option - NethServer/dev#5324

* Wed Jun 07 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.4-1
- Allow WebSocket Proxy Pass - NethServer/dev#5311
- Same FQDN can be set to different virtualhosts - Bug NethServer/dev#5303

* Tue May 30 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.3-1
- Upgrade from NS 6 via backup and restore - NethServer/dev#5234

* Mon May 22 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.3-1
- PKI: SSL chain file not updated after certificate-update - Bug NethServer/dev#5283

* Mon Mar 06 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.2-1
- Migration from sme8 - NethServer/dev#5196

* Wed Nov 09 2016 Davide Principi <davide.principi@nethesis.it> - 3.1.1-1
- Use SSLCipherSuite upstream default from ssl.conf

* Wed Nov 02 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1
- Virtual host inline help does not include plugins - NethServer/dev#5138

* Thu Sep 01 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 3.1.0-1
- Apache vhost-default template expansion - NethServer/dev#5088

* Thu Jul 21 2016 Davide Principi <davide.principi@nethesis.it> - 3.0.0-1
- Incremented major version for ns7
- Reverse proxy page - NethServer/dev#5064

* Thu Jul 07 2016 Stefano Fancello <stefano.fancello@nethesis.it> - 2.6.0-1
- First NS7 release



