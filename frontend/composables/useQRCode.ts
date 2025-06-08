export const useQRCode = () => {
  const generateQRImage = async (qrCodeToken: string) => {
    const QRCode = await import("qrcode");
    const joinUrl = `${window.location.origin}/join/${qrCodeToken}`;
    return await QRCode.toDataURL(joinUrl);
  };

  return { generateQRImage };
};
