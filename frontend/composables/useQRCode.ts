import { useApi } from "./useApi";

export const useQRCode = () => {
  const { api } = useApi();

  const getQRCode = async (groupId: number) => {
    const response = await api<{ qr_code_token: string }>(
      `/groups/${groupId}/qr-code`,
      { method: "GET" }
    );
    return response.qr_code_token;
  };

  const regenerateQRCode = async (groupId: number) => {
    const response = await api<{ qr_code_token: string }>(
      `/groups/${groupId}/qr-code/regenerate`,
      { method: "POST" }
    );
    return response.qr_code_token;
  };

  const generateQRImage = async (qrCodeToken: string) => {
    const QRCode = await import("qrcode");
    const joinUrl = `${window.location.origin}/join/${qrCodeToken}`;
    return await QRCode.toDataURL(joinUrl);
  };

  return { getQRCode, regenerateQRCode, generateQRImage };
};
