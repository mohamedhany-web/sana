                <tr>
                    <td style="padding:20px 28px 28px;border-top:1px solid #f1f5f9;font-family:Tahoma,'Segoe UI',Arial,sans-serif;font-size:12px;line-height:1.7;color:#94a3b8;text-align:right;">
                        هذه رسالة تلقائية من {{ config('app.name', 'Sana') }}.
                        للاستفسار راسلنا على
                        <a href="mailto:{{ config('mail.from.address', 'info@sanaedu.com') }}" style="color:#1D4EDB;text-decoration:none;">{{ config('mail.from.address', 'info@sanaedu.com') }}</a>
                    </td>
                </tr>
            </table>
            <div style="font-family:Tahoma,'Segoe UI',Arial,sans-serif;font-size:11px;color:#94a3b8;margin-top:14px;text-align:center;">
                © {{ date('Y') }} {{ config('app.name', 'Sana') }}
            </div>
        </td>
    </tr>
</table>
</body>
</html>
