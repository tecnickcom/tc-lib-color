Name:      php-tc-lib-color
Version:   %{_version}
Release:   %{_release}%{?dist}
Summary:   Provides tc-lib-color: PHP library to manipulate various color representations

Group:     Development/Libraries/PHP
License:   GNU-LGPL v3
URL:       https://github.com/tecnickcom/tc-lib-color

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-%(%{__id_u} -n)
BuildArch: noarch

Requires:  php >= 5.3.3

%description
Provides tc-lib-color: PHP library to manipulate various color representations (GRAY, RGB, HSL, CMYK) and parse Web colors.

%build
(cd %{_current_directory} && make build)

%install
rm -rf $RPM_BUILD_ROOT
(cd %{_current_directory} && make install DESTDIR=$RPM_BUILD_ROOT)

%clean
rm -rf $RPM_BUILD_ROOT
(cd %{_current_directory} && make clean)

%files
%attr(755,root,root) %{_libpath}

%changelog

* Tue Feb 24 2015 Nicola Asuni <info@tecnick.com> 1.0.0-1
- Initial Commit
