<div class="space-y-6">
    {{-- 警告バナー --}}
    <div class="rounded-md bg-yellow-50 p-4 border border-yellow-300">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">セキュリティに関する注意</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>
                        このエクスポートには、拠点のAPIキーおよびDify接続用のAPIキーが平文で含まれます。
                        ダウンロードしたファイルは厳重に管理してください。
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- エクスポート --}}
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">災害復旧データエクスポート</h3>
            <p class="mt-2 text-sm text-gray-500">
                全拠点、ユーザー、APIキーのデータをJSONファイルとしてエクスポートします。
            </p>

            <div class="mt-6">
                <button type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <i class="fas fa-download mr-2"></i>
                    バックアップデータを生成
                </button>
            </div>
        </div>
    </div>
</div>
